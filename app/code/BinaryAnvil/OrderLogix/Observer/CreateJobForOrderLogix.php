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

namespace BinaryAnvil\OrderLogix\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use BinaryAnvil\JobManager\Model\JobFactory;
use BinaryAnvil\OrderLogix\Jobs\ExportOrdersToOrderLogix;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Logger\Monolog as Logger;

class CreateJobForOrderLogix implements ObserverInterface
{
    /**
     * @var \BinaryAnvil\JobManager\Model\JobFactory
     */
    protected $jobFactory;

    /**
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $logger;

    /**
     * CreateJobForOrderLogix constructor.
     * @param \BinaryAnvil\JobManager\Model\JobFactory $jobFactory
     * @param \Magento\Framework\Logger\Monolog $logger
     */
    public function __construct(JobFactory $jobFactory, Logger $logger)
    {
        $this->jobFactory = $jobFactory;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $orderId = $observer->getOrder()->getId();

        try {
            $this->jobFactory->create()
                ->setType(ExportOrdersToOrderLogix::class)
                ->setPriority(ExportOrdersToOrderLogix::JOB_PRIORITY)
                ->setSource($orderId)
                ->setDetails([ExportOrdersToOrderLogix::JOB_ORDER_ID_FIELD => $orderId])
                ->save();

            $this->logger->log(100, 'Job created for order #' . $orderId);
        } catch (LocalizedException $e) {
            $this->logger->log(100, 'Failed to create Job for order #' . $orderId);
            $this->logger->log(100, 'Exception: ' . $e->getMessage());
        }
    }
}
