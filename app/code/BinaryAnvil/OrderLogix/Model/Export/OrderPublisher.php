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

namespace BinaryAnvil\OrderLogix\Model\Export;

use BinaryAnvil\OrderLogix\Model\Serializer\Order as OrderSerializer;
use BinaryAnvil\OrderLogix\Helper\Config;
use Magento\Framework\Logger\Monolog as Logger;
use BinaryAnvil\OrderLogix\Jobs\ExportOrdersToOrderLogix;

class OrderPublisher
{
    const NOTICE_REQUEST = 'request';

    const NOTICE_RESPONSE = 'response';

    /**
     * @var \BinaryAnvil\OrderLogix\Model\Serializer\Order
     */
    protected $orderSerializer;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var array
     */
    protected $notice = [];

    /**
     * @var \BinaryAnvil\OrderLogix\Helper\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $logger;

    /**
     * OrderPublisher constructor.
     *
     * @param \BinaryAnvil\OrderLogix\Model\Serializer\Order $orderSerializer
     * @param \BinaryAnvil\OrderLogix\Helper\Config $config
     * @param \Magento\Framework\Logger\Monolog $logger
     */
    public function __construct(OrderSerializer $orderSerializer, Config $config, Logger $logger)
    {
        $this->orderSerializer = $orderSerializer;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Publish to OrderLogix
     *
     * @param array $orderIds
     * @return void
     */
    public function toOrderLogix($orderIds = [])
    {
        if (!is_array($orderIds)) {
            $orderIds = [$orderIds];
        }
        $this->errors = [];
        $lastOrderId = false;
        $counter = 0;

        try {
            foreach ($orderIds as $orderId) {
                $lastOrderId = $orderId;
                $this->publish($orderId);
                $counter ++;
            }
        } catch (\Exception $e) {
            $message = sprintf(
                "Failed to export order with id '%s', message: %s",
                $lastOrderId,
                $e->getMessage()
            );

            $this->errors[] = $message;
            $this->logger->log(100, $message);
        }

        $this->logger->log(100, 'Total count of exported products : ' . $counter);
    }

    /**
     * @param int $orderId
     */
    protected function publish($orderId)
    {
        $apiUrl = $this->config->getApiUrl();

        $serializedData = $this->orderSerializer->serialize($orderId);

        $data = 'apipwd=' . $this->config->getApiPassword()
            . '&apitoken=' . $this->config->getApiToken()
            . '&apiuser=' . $this->config->getApiUser()
            . '&authpwd=' . $this->config->getAuthPassword()
            . '&authurl=' . $this->config->getAuthUrl()
            . '&authuser=' . $this->config->getAuthUser()
            . '&data=' . $serializedData;

        $this->notice[static::NOTICE_REQUEST] = $serializedData;

        $httpHeader = [
            'Content-Type: application/x-www-form-urlencoded; ',
            'Content-Length: ' . strlen($data)
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $xmlResponse = curl_exec($ch);

        $this->notice[static::NOTICE_RESPONSE] = $xmlResponse;

        $this->logger->log(100, 'Response:' . $xmlResponse);
        curl_close($ch);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get notices
     *
     * @return array
     */
    public function getNotices()
    {
        return $this->notice;
    }
}
