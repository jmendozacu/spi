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

namespace BinaryAnvil\JobManager\Helper;

use BinaryAnvil\JobManager\Helper\Config as JobConfig;
use BinaryAnvil\JobManager\Logger\Logger;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\Filter;
use Magento\Framework\DataObject;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends AbstractHelper
{
    /**
     * @var \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var \Magento\Framework\Api\SortOrder $sortOrder
     */
    private $sortOrder;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder $filterBuilder
     */
    private $filterBuilder;

    /**
     * @var \BinaryAnvil\JobManager\Helper\Config $config
     */
    private $config;

    /**
     * @var \BinaryAnvil\JobManager\Logger\Logger $_log
     */
    public $log;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     */
    private $timezone;

    /**
     * Data helper constructor
     *
     * @param \Magento\Framework\Api\SortOrder $sortOrder
     * @param \BinaryAnvil\JobManager\Helper\Config $config
     * @param \BinaryAnvil\JobManager\Logger\Logger $logger
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        JobConfig $config,
        Logger $logger,
        SortOrderBuilder $sortOrderBuilder,
        SortOrder $sortOrder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        FilterBuilder $filterBuilder,
        TimezoneInterface $timezone
    ) {
        parent::__construct($context);

        $this->config = $config;
        $this->log = $logger;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->sortOrder = $sortOrder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->timezone = $timezone;
    }

    /**
     * Retrieve job filters
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface
     */
    public function getJobSearchCriteria()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->setFilterGroups($this->getJobsFilter())
            ->setSortOrders($this->getSortOrder())
            ->setPageSize($this->getDefaultLimit())
            ->setCurrentPage(1)
            ->create();

        return $searchCriteria;
    }

    /**
     * Build jobs filters
     * If filter is_array = OR condition will be used
     *
     * @return \Magento\Framework\Api\Search\FilterGroup[]
     */
    private function getJobsFilter()
    {
        $statuses = [JobConfig::JOB_STATUS_PENDING, JobConfig::JOB_STATUS_ERROR];

        return [
            /** Attemts filter */
            $this->filterGroupBuilder->setFilters([
                $this->filterBuilder
                    ->setField(JobConfig::SCHEMA_JOB_FIELD_ATTEMPTS)
                    ->setConditionType('lt')
                    ->setValue($this->getMaxAttempts())
                    ->create(),
            ])->create(),
            /** Status filter */
            $this->filterGroupBuilder->setFilters(
                array_map(function (int $status) : Filter {
                    return $this->filterBuilder
                        ->setField(JobConfig::SCHEMA_JOB_FIELD_STATUS)
                        ->setValue($status)
                        ->create();
                }, $statuses)
            )->create(),
            /** Schedule filter */
            $this->filterGroupBuilder->setFilters([
                    $this->filterBuilder
                        ->setField(JobConfig::SCHEMA_JOB_FIELD_SCHEDULE)
                        ->setConditionType('lt')
                        ->setValue($this->timezone->date()->format(JobConfig::DEFAULT_DATE_TIME_FORMAT))
                        ->create(),
                    $this->filterBuilder
                        ->setField(JobConfig::SCHEMA_JOB_FIELD_SCHEDULE)
                        ->setConditionType('null')
                        ->create(),
            ])->create(),
        ];
    }

    /**
     * Get jobs sort order
     *
     * @return array
     */
    private function getSortOrder()
    {
        return [
            $this->sortOrderBuilder
                ->setDirection(SortOrder::SORT_ASC)
                ->setField(JobConfig::SCHEMA_JOB_FIELD_PRIORITY)
                ->create(),
            $this->sortOrderBuilder
                ->setDirection(SortOrder::SORT_ASC)
                ->setField(JobConfig::SCHEMA_JOB_FIELD_CREATED)
                ->create()
        ];
    }

    /**
     * Validate job
     *
     * @param \BinaryAnvil\JobManager\Api\Data\JobInterface $job
     * @return \Magento\Framework\DataObject
     */
    public function validateJob($job)
    {
        $validation = [
            Config::SCHEMA_JOB_FIELD_TYPE => 'Job type is not set.'
        ];

        $result = new DataObject();

        $valid = true;
        $errors = [];

        foreach ($validation as $field => $error) {
            if (empty($job->getData($field))) {
                $valid = false;
                $errors[] = $error;
            }
        }

        if (!method_exists($job->getType(), 'run')) {
            $valid = false;
            $errors[] = 'Job type "run" method is not exists.';
        }

        $result->setData([
            'valid' => $valid,
            'error' => $errors
        ]);

        return $result;
    }

    /**
     * Get maximum job attempts
     *
     * @return int
     */
    public function getMaxAttempts()
    {
        $attempts = $this->config->getConfig(JobConfig::XML_PATH_ATTEMPTS);

        return (int) !empty($attempts) ? $attempts : 1;
    }

    /**
     * Get jobs per run default limit
     *
     * @return int
     */
    public function getDefaultLimit()
    {
        $limit = $this->config->getConfig(JobConfig::XML_PATH_LIMIT);

        return (int) !empty($limit) ? $limit : JobConfig::DEFAULT_PAGE_SIZE;
    }

    /**
     * Get executed jobs purge threshold
     *
     * @return int
     */
    public function getPurgeExecuted()
    {
        $days = $this->config->getConfig(JobConfig::XML_PATH_PURGE_EXECUTED);
        $days = !empty((int) $days) ? (int) $days : JobConfig::DEFAULT_PURGE_EXECUTED;

        return date('Y-m-d H:i:s', strtotime(' -' . $days . ' day'));
    }

    /**
     * Get archived jobs purge threshold
     *
     * @return string
     */
    public function getArchivePurge()
    {
        $days = $this->config->getConfig(JobConfig::XML_PATH_PURGE_ARCHIVE_KEEP);
        $days = !empty((int) $days) ? (int) $days : JobConfig::DEFAULT_PURGE_ARCHIVE;

        return date('Y-m-d H:i:s', strtotime(' -' . $days . ' day'));
    }

    /**
     * Get error jobs purge threshold
     *
     * @return int
     */
    public function getPurgeError()
    {
        $days = $this->config->getConfig(JobConfig::XML_PATH_PURGE_ERROR);
        $days = !empty((int) $days) ? (int) $days : JobConfig::DEFAULT_PURGE_ERROR;

        return date('Y-m-d H:i:s', strtotime(' -' . $days . ' day'));
    }

    /**
     * Retrive is purge strategy archive
     *
     * @return bool
     */
    public function isStrategyArchive()
    {
        return (bool) (
            $this->config->getConfig(JobConfig::XML_PATH_PURGE_STRATEGY) == JobConfig::JOB_PURGE_STRATEGY_ARCHIVE
        );
    }

    /**
     * Check if module is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->config->getConfig(JobConfig::XML_PATH_IS_ENABLED);
    }
}
