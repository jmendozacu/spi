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
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

use BinaryAnvil\JobManager\Helper\Data;
use BinaryAnvil\JobManager\Api\Data\JobHistoryInterface;
use BinaryAnvil\JobManager\Api\JobHistoryRepositoryInterface;
use BinaryAnvil\JobManager\Model\ResourceModel\JobHistory\CollectionFactory;
use BinaryAnvil\JobManager\Api\Data\JobHistorySearchResultsInterfaceFactory;

class JobHistoryRepository implements JobHistoryRepositoryInterface
{
    /**
     * @var \BinaryAnvil\JobManager\Model\JobHistoryFactory $jobHistoryFactory
     */
    protected $jobHistoryFactory;

    /**
     * @var \BinaryAnvil\JobManager\Model\ResourceModel\JobHistory\CollectionFactory $collectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \BinaryAnvil\JobManager\Api\Data\JobSearchResultsInterfaceFactory $searchResultsFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \BinaryAnvil\JobManager\Helper\Data $helper
     */
    protected $helper;

    /**
     * @param \BinaryAnvil\JobManager\Model\JobHistoryFactory $jobHistoryFactory
     * @param \BinaryAnvil\JobManager\Model\ResourceModel\JobHistory\CollectionFactory $collectionFactory
     * @param \BinaryAnvil\JobManager\Api\Data\JobHistorySearchResultsInterfaceFactory $searchResultsFactory
     * @param \BinaryAnvil\JobManager\Helper\Data $helper
     */
    public function __construct(
        JobHistoryFactory $jobHistoryFactory,
        CollectionFactory $collectionFactory,
        JobHistorySearchResultsInterfaceFactory $searchResultsFactory,
        Data $helper
    ) {
        $this->jobHistoryFactory = $jobHistoryFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function saveMultiple(array $data = [])
    {
        try {
            foreach ($data as $history) {
                $this->save($history);
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the job history: %1',
                $exception->getMessage()
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $data = [])
    {
        try {
            $jobHistory = $this->get(null);

            foreach ($data as $field => $value) {
                if (!empty($value)) {
                    $jobHistory->setData($field, is_array($value) ? json_encode($value) : $value);
                }
            }

            return $jobHistory->save();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the job history: %1',
                $exception->getMessage()
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $jobHistory = $this->jobHistoryFactory->create();

        if (!empty($id)) {
            $jobHistory->load($id);
        }

        return $jobHistory;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        $jobHistory = $this->get($id);

        if (!$jobHistory->getId()) {
            throw new NoSuchEntityException(__(
                'Job history record with id "%1" does not exist.',
                $id
            ));
        }
        return $jobHistory;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        /** @var \BinaryAnvil\JobManager\Model\ResourceModel\JobHistory\Collection $collection */
        $collection = $this->collectionFactory->create();

        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var \Magento\Framework\Api\SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(JobHistoryInterface $jobHistory)
    {
        try {
            /** @var \BinaryAnvil\JobManager\Model\JobHistory $jobHistory */
            $jobHistory->delete();
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the job history: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
