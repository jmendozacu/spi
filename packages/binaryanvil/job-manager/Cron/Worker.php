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

namespace BinaryAnvil\JobManager\Cron;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use BinaryAnvil\JobManager\Api\JobRepositoryInterface;
use BinaryAnvil\JobManager\Model\JobHistoryRepository;
use BinaryAnvil\JobManager\Api\JobRunResultInterface;
use BinaryAnvil\JobManager\Api\Data\JobInterface;
use Magento\Framework\ObjectManagerInterface;
use BinaryAnvil\JobManager\Model\Adapter;
use BinaryAnvil\JobManager\Helper\Config;
use BinaryAnvil\JobManager\Helper\Data;

class Worker
{
    const SAFETY_BREAK = 50;

    /**
     * @var string $jobRunStart
     */
    private $jobRunStart = 'Running jobs by schedule...';

    /**
     * @var string $jobRunAborted
     */
    private $jobRunAborted = 'Aborting. Previous job is still running...';

    /**
     * @var \BinaryAnvil\JobManager\Api\JobRepositoryInterface $jobRepository
     */
    private $jobRepository;

    /**
     * @var \BinaryAnvil\JobManager\Api\JobRunResultInterface $jobRunResult
     */
    private $jobRunResult;

    /**
     * @var \BinaryAnvil\JobManager\Api\Data\JobInterface $jobInterface
     */
    private $jobInterface;

    /**
     * @var \BinaryAnvil\JobManager\Model\Adapter $adapter
     */
    private $adapter;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     */
    private $timezone;

    /**
     * @var \Magento\Framework\ObjectManagerInterface $objectManager
     */
    private $objectManager;

    /**
     * @var string $jobSuccess
     */
    private $jobSuccess = Config::MESSAGE_JOB_SUCCESS;

    /**
     * @var string $jobFailed
     */
    private $jobFailed = Config::MESSAGE_JOB_FAILED;

    /**
     * @var \BinaryAnvil\JobManager\Model\JobHistoryRepository $jobHistoryRepository
     */
    private $jobHistoryRepository;

    /**
     * @param \BinaryAnvil\JobManager\Helper\Data $helper
     * @param \BinaryAnvil\JobManager\Model\Adapter $adapter
     * @param \BinaryAnvil\JobManager\Api\JobRepositoryInterface $jobRepository
     * @param \BinaryAnvil\JobManager\Api\Data\JobInterface $jobInterface
     * @param \BinaryAnvil\JobManager\Api\JobRunResultInterface $jobRunResult
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \BinaryAnvil\JobManager\Model\JobHistoryRepository $jobHistoryRepository
     */
    public function __construct(
        Data $helper,
        Adapter $adapter,
        JobRepositoryInterface $jobRepository,
        JobInterface $jobInterface,
        JobRunResultInterface $jobRunResult,
        TimezoneInterface $timezone,
        ObjectManagerInterface $objectManager,
        JobHistoryRepository $jobHistoryRepository
    ) {
        $this->helper = $helper;
        $this->adapter = $adapter;
        $this->timezone = $timezone;
        $this->jobRepository = $jobRepository;
        $this->jobInterface = $jobInterface;
        $this->jobRunResult = $jobRunResult;
        $this->objectManager = $objectManager;
        $this->jobHistoryRepository = $jobHistoryRepository;
    }

    /**
     * Execute scheduled jobs
     *
     * Safety break is set up for 50 seconds
     * to reflect crontab.xml settings.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        $break = $this->timezone->scopeTimeStamp() + static::SAFETY_BREAK;

        if (!$this->helper->isEnabled()) {
            return false;
        }

        if (!$this->adapter->lock()) {
            $this->helper->log->info($this->jobRunAborted);
            return false;
        }

        try {
            $searchCriteria = $this->helper->getJobSearchCriteria();
            $jobs = $this->jobRepository->getList($searchCriteria)->getItems();

            if (empty($jobs)) {
                return false;
            }

            $this->helper->log->info($this->jobRunStart);

            foreach ($jobs as $job) {
                if ($this->timezone->scopeTimeStamp() > $break) {
                    break;
                }
                $this->work($job);
            }

            return true;
        } catch (\Exception $e) {
            $this->helper->log->info($this->jobRunStart);
            $this->helper->log->critical($e);
        } finally {
            $this->adapter->unlock();
        }

        return false;
    }

    /**
     * Execute single job
     *
     * @param \BinaryAnvil\JobManager\Api\Data\JobInterface $job
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     */
    private function work($job)
    {
        $time = $this->timezone->scopeTimeStamp();

        $type = $job->getType();

        $validate = $this->helper->validateJob($job);

        $dataHistory = [];

        $dataHistoryHeader = [
            Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE_TIME => $time,
            Config::SCHEMA_JOB_FIELD_ID => $job->getId()
        ];

        $data = [
            Config::SCHEMA_JOB_FIELD_ATTEMPTS => ($job->getAttempt() + 1),
            Config::SCHEMA_JOB_FIELD_LAST_ATTEMPT => $time,
            Config::SCHEMA_JOB_FIELD_STATUS => Config::JOB_STATUS_RUNNING,
        ];

        $this->jobRepository->save($job, $data);

        if (!$validate->getData('valid')) {
            $data[Config::SCHEMA_JOB_FIELD_STATUS] = Config::JOB_STATUS_ERROR;

            if (!empty($validate->getData('error'))) {
                $data[Config::SCHEMA_JOB_FIELD_LAST_ERROR] = $validate->getData('error');

                $dataHistory[] = array_merge([
                    Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE => $validate->getData('error'),
                    Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE_TYPE => Config::JOB_MESSAGE_TYPE_ERROR,
                ], $dataHistoryHeader);
            }

            $this->jobRepository->save($job, $data);
            $this->jobHistoryRepository->saveMultiple($dataHistory);

            return false;
        }

        /** @var \BinaryAnvil\JobManager\Api\JobRunInterface $class */
        $class = $this->objectManager->create($type);

        try {
            /** @var \BinaryAnvil\JobManager\Api\JobRunResultInterface $result */
            $result = $class->run($job);

            if (!$result instanceof JobRunResultInterface) {
                return false;
            }

            $dataHistory = $this->getJobHistory($result, $dataHistoryHeader);

            if ($result->isDone()) {
                $data[Config::SCHEMA_JOB_FIELD_STATUS] = Config::JOB_STATUS_EXECUTED;
                $data[Config::SCHEMA_JOB_FIELD_EXECUTED] = $time;

                $this->helper->log->success(
                    __($this->jobSuccess, ['type' => $job->getType(), 'id' => $job->getId()])
                );
            } else {
                $data[Config::SCHEMA_JOB_FIELD_STATUS] = Config::JOB_STATUS_ERROR;

                if (!empty($result->getErrors())) {
                    $data[Config::SCHEMA_JOB_FIELD_LAST_ERROR] = $result->getErrors();
                } else {
                    $data[Config::SCHEMA_JOB_FIELD_LAST_ERROR] = strtr(
                        $this->jobFailed,
                        ['%type' => $job->getType(), '%id' => $job->getId()]
                    );
                }

                $this->helper->log->critical(
                    __($this->jobFailed, ['type' => $job->getType(), 'id' => $job->getId()])
                );
            }

            $dataHistory[] = $this->getResultHistory($result, $job, $dataHistoryHeader);

            $this->jobRepository->save($job, $data);
            $this->jobHistoryRepository->saveMultiple($dataHistory);

            return $result->isDone();
        } catch (\Exception $e) {
            $this->helper->log->critical($e);

            return false;
        }
    }

    /**
     * Save job history messages
     *
     * @param \BinaryAnvil\JobManager\Api\JobRunResultInterface $result
     * @param array $historyHeader
     * @return array
     */
    public function getJobHistory($result, $historyHeader)
    {
        $dataHistory = [];

        if (!empty($result->getNotice())) {
            $dataHistory[] = array_merge([
                Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE_TYPE => Config::JOB_MESSAGE_TYPE_NOTICE,
                Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE => $result->getNotice(),
            ], $historyHeader);
        }

        if (!empty($result->getRequest())) {
            $dataHistory[] = array_merge([
                Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE_TYPE => Config::JOB_MESSAGE_TYPE_REQUEST,
                Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE => $result->getRequest(),
            ], $historyHeader);
        }

        if (!empty($result->getResponse())) {
            $dataHistory[] = array_merge([
                Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE_TYPE => Config::JOB_MESSAGE_TYPE_RESPONSE,
                Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE => $result->getResponse(),
            ], $historyHeader);
        }

        return $dataHistory;
    }

    /**
     * Get result history
     *
     * @param \BinaryAnvil\JobManager\Api\JobRunResultInterface $result
     * @param \BinaryAnvil\JobManager\Api\Data\JobInterface $job
     * @param array $dataHistoryHeader
     * @return array
     */
    private function getResultHistory($result, $job, $dataHistoryHeader)
    {
        $history = [];

        if ($result->isDone()) {
            $history[Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE_TYPE] = Config::JOB_MESSAGE_TYPE_SUCCESS;

            if (!empty($result->getSuccess())) {
                $history[Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE] = $result->getSuccess();
            } else {
                $history[Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE] = strtr(
                    $this->jobSuccess,
                    ['%type' => $job->getType(), '%id' => $job->getId()]
                );
            }
        } else {
            $history[Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE_TYPE] = Config::JOB_MESSAGE_TYPE_ERROR;

            if (!empty($result->getErrors())) {
                $history[Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE] = $result->getErrors();
            } else {
                $history[Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE] = strtr(
                    $this->jobFailed,
                    ['%type' => $job->getType(), '%id' => $job->getId()]
                );
            }
        }

        $history = array_merge($history, $dataHistoryHeader);

        return $history;
    }
}
