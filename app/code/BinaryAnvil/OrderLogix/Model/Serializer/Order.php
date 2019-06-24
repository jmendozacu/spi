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

namespace BinaryAnvil\OrderLogix\Model\Serializer;

use XMLWriter;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Logger\Monolog as Logger;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Order
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \XMLWriter
     */
    protected $writer;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;

    /**
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * Order constructor.
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \XMLWriter $writer
     * @param \Magento\Directory\Model\RegionFactory  $regionFactory
     * @param \Magento\Framework\Logger\Monolog  $logger
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface  $timezone
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        XMLWriter $writer,
        RegionFactory $regionFactory,
        Logger $logger,
        TimezoneInterface $timezone
    ) {
        $this->orderRepository = $orderRepository;
        $this->writer = $writer;
        $this->regionFactory = $regionFactory;
        $this->logger = $logger;
        $this->timezone = $timezone;
    }

    /**
     * @param int $orderId
     * @return string
     */
    public function serialize($orderId)
    {
        $this->writer->openMemory();
        $this->writer->setIndent(true);
        $this->writer->setIndentString(str_repeat(' ', 4));

        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $order = $this->getOrder($orderId);
        /** @var \Magento\Sales\Api\Data\OrderPaymentInterface $payment */
        $payment = $order->getPayment();
        /** @var \Magento\Sales\Api\Data\OrderAddressInterface $billingAddress */
        $billingAddress = $order->getBillingAddress();
        /** @var \Magento\Sales\Api\Data\OrderAddressInterface $shippingAddress */
        $shippingAddress = $order->getShippingAddress();
        $expDate = ($payment->getCcExpMonth() < 10 ? '0' : '') . $payment->getCcExpMonth()
            . substr($payment->getCcExpYear(), 2);
        $paymentAdditionalInfo = $payment->getAdditionalInformation();
        $ccType = $paymentAdditionalInfo['card_type'] ?? '';
        if (array_key_exists('profile_id', $paymentAdditionalInfo)
            && array_key_exists('payment_id', $paymentAdditionalInfo)) {
            $token = $paymentAdditionalInfo['profile_id'] . ',' . $paymentAdditionalInfo['payment_id'];
        } else {
            $token = '';
        }

        $ccBin = array_key_exists('cc_bin', $paymentAdditionalInfo) ? $paymentAdditionalInfo['cc_bin'] : '';

        $date = $this->timezone->date($order->getCreatedAt());
        $date->setTimezone(new \DateTimeZone('PST'));
        $orderCreatedAt = $date->format('m/d/Y H:i');

        $elements = [
            [
                'name' => 'Order',
                'value' => [
                    ['name' => 'CustomerID', 'value' => '', 'cdata' => true],
                    ['name' => 'CustomerType', 'value' => '', 'cdata' => true],
                    ['name' => 'BillingAddress', 'value' => $this->getBillingAddressData($billingAddress)],
                    ['name' => 'ShipToAddress', 'value' => $this->getShippingAddressData($shippingAddress)],
                    ['name' => 'ShippingCode', 'value' => 'ECON', 'cdata' => false],
                    ['name' => 'OrderType', 'value' => 'Y', 'cdata' => false],
                    ['name' => 'Email', 'value' => $order->getCustomerEmail(), 'cdata' => true],
                    ['name' => 'CcType', 'value' => strtoupper($ccType), 'cdata' => false],
                    ['name' => 'CcFirstSixDigits', 'value' => $ccBin, 'cdata' => false],
                    ['name' => 'CcLastFourDigits', 'value' => $payment->getCcLast4(), 'cdata' => false],
                    ['name' => 'cc_exp_date', 'value' => $expDate, 'cdata' => false],
                    ['name' => 'AMZTansactionID', 'value' => '', 'cdata' => false],
                    ['name' => 'PayPalTransactionID', 'value' => '', 'cdata' => false],
                    ['name' => 'PONumber', 'value' => $payment->getPoNumber(), 'cdata' => false],
                    ['name' => 'DateOrdered', 'value' => $orderCreatedAt, 'cdata' => false],
                    ['name' => 'TaxAmount', 'value' => $order->getTaxAmount(), 'cdata' => false],
                    ['name' => 'ShipChargeAmount', 'value' => $order->getShippingAmount(), 'cdata' => false],
                    ['name' => 'ShippingInformation', 'value' => '', 'cdata' => false],
                    ['name' => 'SalesPerson', 'value' => '', 'cdata' => false],
                    ['name' => 'Token', 'value' => $token, 'cdata' => false],
                    ['name' => 'SourceCode', 'value' => $order->getCouponCode(), 'cdata' => true],
                    ['name' => 'CouponValue', 'value' => '0.00', 'cdata' => false],
                    ['name' => 'WebOrderNumber', 'value' => $order->getIncrementId(), 'cdata' => false],
                    ['name' => 'GiftCertNumber', 'value' => $order->getGiftCards(), 'cdata' => true],
                    ['name' => 'GiftCertificateValue', 'value' => $order->getGiftCardsAmount(), 'cdata' => false],
                    ['name' => 'GiftCertificate1AuthorizationCode', 'value' => '', 'cdata' => false],
                    ['name' => 'OrderItems', 'value' => $this->getOrderItemsData($order->getItems())]
                ]
            ]
        ];

        $this->writeElements($elements);

        $xml = $this->writer->outputMemory();

        $this->logger->log(100, 'Reauest:\n' . $xml);
        return $xml;
    }

    /**
     * @param array $elements
     */
    private function writeElements($elements = [])
    {
        foreach ($elements as $element) {
            $this->writer->startElement($element['name']);
            if (is_array($element['value'])) {
                $this->writeElements($element['value']);
            } else {
                if ($element['cdata']) {
                    $this->writer->startCdata();
                }
                $this->writer->text($element['value']);
                if ($element['cdata']) {
                    $this->writer->endCdata();
                }
            }
            $this->writer->endElement();
        }
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderAddressInterface $billingAddress
     * @return array
     */
    private function getBillingAddressData(OrderAddressInterface $billingAddress)
    {
        return [
            ['name' => 'BILL_COMPANY', 'value' => $billingAddress->getCompany(), 'cdata' => true],
            ['name' => 'BILL_FIRST_NAME', 'value' => $billingAddress->getFirstname(), 'cdata' => true],
            ['name' => 'BILL_LAST_NAME', 'value' => $billingAddress->getLastname(), 'cdata' => true],
            ['name' => 'BILL_ADDRESS1', 'value' => $billingAddress->getStreet()[0], 'cdata' => true],
            ['name' => 'BILL_ADDRESS2', 'value' => $billingAddress->getStreet()[1] ?? '', 'cdata' => true],
            ['name' => 'BILL_CITY', 'value' => $billingAddress->getCity(), 'cdata' => true],
            ['name' => 'BILL_STATE', 'value' => $billingAddress->getRegionCode(), 'cdata' => true],
            ['name' => 'BILL_ZIPCODE', 'value' => $billingAddress->getPostcode(), 'cdata' => true],
            ['name' => 'COUNTY', 'value' => '', 'cdata' => true],
            ['name' => 'COUNTRY', 'value' => $billingAddress->getCountryId(), 'cdata' => true],
            ['name' => 'BILL_PHONE_NUMBER', 'value' =>$billingAddress->getTelephone(), 'cdata' => true],
            ['name' => 'BILL_PHONE_2', 'value' => '', 'cdata' => true]
        ];
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderAddressInterface  $shippingAddress
     * @return array
     */
    private function getShippingAddressData(OrderAddressInterface $shippingAddress)
    {
        return [
            ['name' => 'SHIP_TO_COMPANY', 'value' => $shippingAddress->getCompany(), 'cdata' => true],
            ['name' => 'SHIP_TO_FIRST_NAME', 'value' => $shippingAddress->getFirstname(), 'cdata' => true],
            ['name' => 'SHIP_TO_LAST_NAME', 'value' => $shippingAddress->getLastname(), 'cdata' => true],
            ['name' => 'SHIP_TO_ADDRESS1', 'value' => $shippingAddress->getStreet()[0], 'cdata' => true],
            ['name' => 'SHIP_TO_ADDRESS2', 'value' => $shippingAddress->getStreet()[1] ?? '', 'cdata' => true],
            ['name' => 'SHIP_TO_CITY', 'value' => $shippingAddress->getCity(), 'cdata' => true],
            ['name' => 'SHIP_TO_STATE', 'value' => $shippingAddress->getRegionCode(), 'cdata' => true],
            ['name' => 'SHIP_TO_ZIPCODE', 'value' => $shippingAddress->getPostcode(), 'cdata' => true],
            ['name' => 'SCOUNTY', 'value' => '', 'cdata' => true],
            ['name' => 'SCOUNTRY', 'value' => $shippingAddress->getCountryId(), 'cdata' => true],
            ['name' => 'SHIP_TO_PHONE_NUMBER', 'value' => $shippingAddress->getTelephone(), 'cdata' => true],
            ['name' => 'SHIP_TO_PHONE_2', 'value' => '', 'cdata' => true]
        ];
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderItemInterface[] $items
     * @return array
     */
    private function getOrderItemsData($items = [])
    {
        $itemData = [];

        foreach ($items as $item) {
            if ($item->getPrice() > 0) {
                $itemData[] = ['name' => 'OrderItem', 'value' => [
                    ['name' => 'ProductNumber', 'value' => $item->getSku(), 'cdata' => false],
                    ['name' => 'OrderQuantity', 'value' => (int)$item->getQtyOrdered(), 'cdata' => false],
                    ['name' => 'UnitPrice', 'value' => $item->getPrice(), 'cdata' => false],
                    ['name' => 'PersonalizationInstructions', 'value' => '', 'cdata' => true],
                    ['name' => 'EmbroideryPersonalizationCharges', 'value' => '0.00', 'cdata' => false],
                    ['name' => 'EngravingPersonalizationCharges', 'value' => '0.00', 'cdata' => false],
                    ['name' => 'Taxable', 'value' => 'Y', 'cdata' => false]
                ]
                ];
            }
        }

        return $itemData;
    }

    /**
     * @param int $orderId
     * @return bool|\Magento\Sales\Api\Data\OrderInterface
     */
    protected function getOrder($orderId)
    {
        $result = false;

        try {
            $result = $this->orderRepository->get($orderId);
        } catch (NoSuchEntityException $e) {
            $message = 'Order with id :"' . $orderId . '" does not exist.';
            $this->logger->log(100, $message);
        }

        return $result;
    }
}
