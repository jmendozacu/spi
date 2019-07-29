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

namespace BinaryAnvil\JobManager\Block\Adminhtml\Job\Grid;

use Magento\Backend\Block\Template;
use BinaryAnvil\JobManager\Helper\Config;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Api\SearchCriteriaInterface;
use BinaryAnvil\JobManager\Model\ResourceModel\Job\CollectionFactory;

class QuickStats extends Template
{
    /**
     * @var \BinaryAnvil\JobManager\Model\ResourceModel\Job\CollectionFactory
     */
    protected $jobCollectionFactory;

    /**
     * @var \BinaryAnvil\JobManager\Api\Data\JobSearchResultsInterface $jobs
     */
    protected $jobs;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface $searchCriteriaInterface
     */
    protected $searchCriteriaInterface;

    /**
     * @var \BinaryAnvil\JobManager\Helper\Config $config
     */
    protected $config;

    /**
     * @param \BinaryAnvil\JobManager\Model\ResourceModel\Job\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteriaInterface
     * @param \BinaryAnvil\JobManager\Helper\Config $config
     * @param \Magento\Backend\Block\Template\Context $context
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        SearchCriteriaInterface $searchCriteriaInterface,
        Config $config,
        Context $context
    ) {
        parent::__construct($context);

        $this->config = $config;
        $this->searchCriteriaInterface = $searchCriteriaInterface;
        $this->jobCollectionFactory = $collectionFactory;
    }

    /**
     * Retrieve job stats
     *
     * @return array
     */
    public function getStats()
    {
        $stats = [
            'priority' => $this->getPriorities(),
            'status' => $this->getStatuses(),
        ];

        /** @var \BinaryAnvil\JobManager\Api\Data\JobInterface $job */
        foreach ($this->getJobs() as $job) {
            if (!empty($job->getPriority())) {
                $stats['priority'][$job->getPriority()]['count'] =
                    !empty($stats['priority'][$job->getPriority()]['count']) ?
                        $stats['priority'][$job->getPriority()]['count'] + 1 : 1;
            }

            if ($job->getStatus() == 0 || !empty($job->getStatus())) {
                $stats['status'][$job->getStatus()]['count'] =
                    !empty($stats['status'][$job->getStatus()]['count']) ?
                        $stats['status'][$job->getStatus()]['count'] + 1 : 1;
            }
        }

        return $stats;
    }

    /**
     * Retrieve priority percentage
     *
     * @param int $current
     * @param int $total
     * @return float
     */
    public function getPercentage($current, $total = 0)
    {
        if ($total == 0) {
            return number_format($total, 2);
        }

        return number_format(($current / $total) * 100, 2);
    }

    /**
     * Get total jobs count
     *
     * @return int
     */
    public function getTotalJobs()
    {
        return $this->getJobs()->getSize();
    }

    /**
     * Get job priorities
     *
     * @return array
     */
    protected function getPriorities()
    {
        $priorities = [];
        foreach ($this->config->priorityToArray() as $ind => $name) {
            $priorities[$ind] = [
                'name' => $name,
                'class' => 'meter-priority-' . strtolower($name),
                'count' => 0,
            ];
        }

        return $priorities;
    }

    /**
     * Get job statuses
     *
     * @return array
     */
    protected function getStatuses()
    {
        $statuses = [];
        foreach ($this->config->statusToArray() as $ind => $name) {
            $statuses[$ind] = [
                'name' => $name,
                'class' => 'meter-status-' . strtolower($name),
                'count' => 0,
            ];
        }

        return $statuses;
    }

    /**
     * Retrieve jobs
     *
     * @return \BinaryAnvil\JobManager\Model\ResourceModel\Job\Collection
     */
    protected function getJobs()
    {
        if (!$this->jobs) {
            $this->jobs = $this->jobCollectionFactory->create()
                ->addFieldToSelect('priority')
                ->addFieldToSelect('status');
        }
        return $this->jobs;
    }
}
