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
 * @package     BinaryAnvil_OrderLogix
 * @copyright   Copyright (c) 2017-2018 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\OrderLogix\Jobs;

use BinaryAnvil\JobManager\Api\JobRunInterface;
use BinaryAnvil\JobManager\Api\JobRunResultInterface;
use BinaryAnvil\JobManager\Helper\Config;
use BinaryAnvil\OrderLogix\Model\Export\OrderPublisherFactory;

class ExportOrdersToOrderLogix implements JobRunInterface
{
    const JOB_PRIORITY = Config::JOB_PRIORITY_LOW;

    const JOB_ORDER_ID_FIELD = 'order_id';

    /**
     * @var \BinaryAnvil\JobManager\Api\JobRunResultInterface
     */
    protected $jobRunResult;

    /**
     * @var \BinaryAnvil\OrderLogix\Model\Export\OrderPublisherFactory $orderPublisher
     */
    protected $orderPublisher;

    /**
     * ExportOrdersToOrderLogix constructor.
     *
     * @param \BinaryAnvil\JobManager\Api\JobRunResultInterface $jobRunResult
     * @param \BinaryAnvil\OrderLogix\Model\Export\OrderPublisherFactory $orderPublisher
     */
    public function __construct(
        JobRunResultInterface $jobRunResult,
        OrderPublisherFactory $orderPublisher
    ) {
        $this->jobRunResult = $jobRunResult;
        $this->orderPublisher = $orderPublisher;
    }

    /**
     * Run job
     *
     * @param \BinaryAnvil\JobManager\Api\Data\JobInterface $job
     * @return \BinaryAnvil\JobManager\Api\JobRunResultInterface
     */
    public function run($job)
    {
        $result = $this->jobRunResult;

        if (empty($job->getDetails())) {
            return $result->setDone(true)
                ->setErrors(['Job details were empty.']);
        }

        try {
            $details = $job->getDetails();
            /** @var \BinaryAnvil\OrderLogix\Model\Export\OrderPublisher $publisher */
            $publisher = $this->orderPublisher->create();

            if (!empty($details[static::JOB_ORDER_ID_FIELD])) {
                $publisher->toOrderLogix($details[static::JOB_ORDER_ID_FIELD]);

                if (!empty($publisher->getNotices())) {
                    foreach ($publisher->getNotices() as $action => $message) {
                        if ($action == $publisher::NOTICE_REQUEST) {
                            $result->setRequest([$message]);
                        }
                        if ($action == $publisher::NOTICE_RESPONSE) {
                            $result->setResponse([$message]);
                        }
                    }
                }

                if (!empty($publisher->getErrors())) {
                    $result->setErrors($publisher->getErrors());
                    return $result->setDone(false);
                }

                $result->setSuccess('Order ID #' . $details[static::JOB_ORDER_ID_FIELD] . ' exported successfully.');

                return $result->setDone(true);
            }
        } catch (\Exception $e) {
            return $result->setDone(false)
                ->setErrors([$e->getMessage()]);
        }

        return $result->setDone(false);
    }
}
