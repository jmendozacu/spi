<?php
/**
 * Binary Anvil, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Binary Anvil, Inc. Software Agreement
 * that is bundled with this package in the file LICENSE_BAS.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.binaryanvil.com/software/license/
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@binaryanvil.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this software to
 * newer versions in the future. If you wish to customize this software for
 * your needs please refer to http://www.binaryanvil.com/software for more
 * information.
 *
 * @category    BinaryAnvil
 * @package     JobManager
 * @copyright   Copyright (c) 2016-present Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\JobManager\Model;

use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;

use BinaryAnvil\JobManager\Helper\Data;
use BinaryAnvil\JobManager\Helper\Config;
use BinaryAnvil\JobManager\Model\JobFactory;
use BinaryAnvil\JobManager\Api\Data\JobInterface;
use BinaryAnvil\JobManager\Api\JobRepositoryInterface;
use BinaryAnvil\JobManager\Model\ResourceModel\Job\Collection;
use BinaryAnvil\JobManager\Api\Data\JobSearchResultsInterfaceFactory;
use BinaryAnvil\JobManager\Model\ResourceModel\Job\CollectionFactory;

class JobRepository implements JobRepositoryInterface
{
    /**
     * @var \BinaryAnvil\JobManager\Model\JobFactory $jobFactory
     */
    protected $jobFactory;

    /**
     * @var \BinaryAnvil\JobManager\Model\ResourceModel\Job\CollectionFactory $collectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \BinaryAnvil\JobManager\Api\Data\JobSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \BinaryAnvil\JobManager\Helper\Data $helper
     */
    protected $helper;

    /**
     * JobRepository constructor
     *
     * @param \BinaryAnvil\JobManager\Helper\Data $helper
     * @param \BinaryAnvil\JobManager\Model\JobFactory $jobFactory
     * @param \BinaryAnvil\JobManager\Model\ResourceModel\Job\CollectionFactory $collectionFactory
     * @param \BinaryAnvil\JobManager\Api\Data\JobSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        JobFactory $jobFactory,
        CollectionFactory $collectionFactory,
        JobSearchResultsInterfaceFactory $searchResultsFactory,
        Data $helper
    ) {
        $this->jobFactory = $jobFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(JobInterface $job, array $data = [])
    {
        try {
            $job = $this->get($job->getId());
        } catch (NoSuchEntityException $e) {
            $job = $this->jobFactory->create();
        }

        $job = $this->setJobData($job, $data);

        try {
            $job->save();
        } catch (\Exception $e) {
            $this->helper->log->critical($e);
        }

        return $job;
    }

    /**
     * Validate and set job data
     *
     * @param \BinaryAnvil\JobManager\Api\Data\JobInterface $job
     * @param array $data
     * @return \BinaryAnvil\JobManager\Api\Data\JobInterface
     */
    protected function setJobData($job, $data = [])
    {
        $defaults = [
            Config::SCHEMA_JOB_FIELD_STATUS => Config::JOB_STATUS_PENDING,
            Config::SCHEMA_JOB_FIELD_PRIORITY => Config::JOB_PRIORITY_LOWEST,
            Config::SCHEMA_JOB_FIELD_DETAILS => [],
        ];

        $data = array_merge($defaults, $job->getData(), $data);

        foreach ($job->getData() as $field => $value) {
            if (!empty($data[$field]) && $data[$field] != $value) {
                $job->setData($field, is_array($data[$field]) ? json_encode($data[$field]) : $data[$field]);
            }
        }

        return $job;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $job = $this->jobFactory->create()->load($id);

        return $job;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \BinaryAnvil\JobManager\Model\ResourceModel\Job\Collection $collection */
        $collection = $this->collectionFactory->create();

        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }

        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        foreach ((array) $searchCriteria->getSortOrders() as $sortOrder) {
            $collection->addOrder(
                $sortOrder->getField(),
                ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }

        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->load();

        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * Add filter group to collection
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \BinaryAnvil\JobManager\Model\ResourceModel\Job\Collection $collection
     * @return void
     */
    public function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $collection)
    {
        $fields = [];
        $conditions = [];

        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $conditions[] = [$condition => $filter->getValue()];
            $fields[] = $filter->getField();
        }

        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete(JobInterface $job)
    {
        try {
            $job = $this->get($job->getId());
            $job->delete();

            return true;
        } catch (\Exception $e) {
            $this->helper->log->critical($e);
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($id)
    {
        try {
            $job = $this->get($id);
            $job->delete();

            return true;
        } catch (\Exception $e) {
            $this->helper->log->critical($e);
            return false;
        }
    }
}
