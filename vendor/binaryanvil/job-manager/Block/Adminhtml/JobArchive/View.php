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

namespace BinaryAnvil\JobManager\Block\Adminhtml\JobArchive;

use BinaryAnvil\JobManager\Helper\Config;
use BinaryAnvil\JobManager\Model\JobHistoryRepository;

use Magento\Framework\Registry;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template\Context;

class View extends Template
{
    /**
     * @var \Magento\Framework\Registry $coreRegistry
     */
    protected $coreRegistry;

    /**
     * @var \BinaryAnvil\JobManager\Helper\Config $config
     */
    protected $config;

    /**
     * @var \BinaryAnvil\JobManager\Api\Data\JobInterface $job
     */
    protected $job;

    /**
     * @var \BinaryAnvil\JobManager\Model\JobHistoryRepository $jobHistoryRepository
     */
    protected $jobHistoryRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\SortOrderBuilder $sortOrderBuider
     */
    protected $sortOrderBuider;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    protected $serializer;

    /**
     * View job block constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \BinaryAnvil\JobManager\Helper\Config $config
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \BinaryAnvil\JobManager\Model\JobHistoryRepository $jobHistoryRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\SortOrderBuilder $sortOrderBuider
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        Registry $coreRegistry,
        JobHistoryRepository $jobHistoryRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuider,
        Json $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->coreRegistry = $coreRegistry;
        $this->config = $config;
        $this->jobHistoryRepository = $jobHistoryRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuider = $sortOrderBuider;
        $this->serializer = $serializer;
    }

    /**
     * Retrieve job status
     *
     * @return string
     */
    public function getJobStatus()
    {
        $statuses = $this->config->statusToArray();
        $label = 'Undefined';

        if ($status = $statuses[$this->getJob()->getStatus()]) {
            $label = '
                <span class="jobmanager-statuses jobmanager-status-' . strtolower($status) . '">
                    ' . $status . '            
                </span>            
            ';
        }

        return $label;
    }

    /**
     * Retrieve job priority
     *
     * @return string
     */
    public function getJobPriority()
    {
        $priorities = $this->config->priorityToArray();
        $label = 'Undefined';

        if ($priority = $priorities[$this->getJob()->getPriority()]) {
            $label = '
                <span class="jobmanager-priorities jobmanager-priority-' . strtolower($priority) . '">
                    ' . $priority . '            
                </span>            
            ';
        }

        return $label;
    }

    /**
     * Retrieve job
     *
     * @return \BinaryAnvil\JobManager\Api\Data\JobInterface
     */
    public function getJob()
    {
        if (!$this->job) {
            $this->job = $this->coreRegistry->registry('current_job');
        }

        return $this->job;
    }

    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('binaryanvil_jobmanager/*/archive');
    }

    /**
     * Get job history list
     *
     * @return array
     */
    public function getJobHistory()
    {
        $jobHistory = [];
        $job = $this->getJob();
        
        if (isset($job)) {
            $sortOrder = $this->sortOrderBuider
                ->setField(Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE_TIME)
                ->setDirection(SortOrder::SORT_DESC)
                ->create();

            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(Config::SCHEMA_JOB_FIELD_ID, $job->getId())
                ->addSortOrder($sortOrder)
                ->create();

            $jobHistory = $this->jobHistoryRepository->getList($searchCriteria)->getItems();
        }
        
        return $jobHistory;
    }

    /**
     * @param $message
     * @return string
     */
    public function getJobMessage($message)
    {
        try {
            $data = $this->serializer->unserialize($message);
            return implode(',', $data);
        } catch (\InvalidArgumentException $e) {
            return $message;
        }
    }

    /**
     * Retrieve job message type
     *
     * @param $type
     * @return string
     */
    public function getJobMessageType($type)
    {
        $messagesTypes = $this->config->messagesTypeToArray();
        $label = '';

        if ($type = $messagesTypes[$type]) {
            $label = '
                <span class="jobhitory-message jobhitory-message-' . strtolower($type) . '">
                    ' . $type . '            
                </span>            
            ';
        }

        return $label;
    }

    /**
     * Check if job message type is request or response
     *
     * @param int $type
     * @return bool
     */
    public function isCodeBlock($type)
    {
        if ($type == Config::JOB_MESSAGE_TYPE_REQUEST || $type == Config::JOB_MESSAGE_TYPE_RESPONSE) {
            return true;
        }

        return false;
    }
}
