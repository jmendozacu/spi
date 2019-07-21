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
 * @package     BinaryAnvil_Customer
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\Customer\Block;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Address\Config as AddressConfig;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Model\Address\Mapper;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollection;
use Magento\Framework\Registry;

class AccountData extends Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var \Magento\Customer\Model\Address\Config
     */
    protected $addressConfig;

    /**
     * @var \Magento\Customer\Model\Address\Mapper
     */
    protected $addressMapper;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollection;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param \Magento\Customer\Model\Address\Config $addressConfig
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Customer\Model\Address\Mapper $addressMapper
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection
     * @param \Magento\Framework\Registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        SessionFactory $customerSession,
        AddressConfig $addressConfig,
        AddressRepositoryInterface $addressRepository,
        Mapper $addressMapper,
        OrderCollection $orderCollection,
        Registry $registry,
        array $data = []
    ) {
        $this->customerSession = $customerSession->create();
        $this->addressConfig = $addressConfig;
        $this->addressRepository = $addressRepository;
        $this->addressMapper = $addressMapper;
        $this->orderCollection = $orderCollection;
        $registry->register('tokenbase_method', 'authnetcim');

        parent::__construct($context, $data);
    }

    /**
     * Get customer's saved address
     * @return string|false
     */
    public function getSavedAddress()
    {
        if ($customer = $this->getCustomer()) {
            return $this->getAddressHtml($this->getAddressById($customer->getDefaultShipping()));
        }
        return false;
    }

    /**
     * Get customer's saved address
     * @return string
     */
    public function getSavedAddressEditUrl()
    {
        return $this->getUrl('customer/address');
    }

    /**
     * @return string
     */
    public function getAddressEditUrl()
    {
        return $this->getUrl('customer/address/edit', [
            '_secure' => true,
            'id' => $this->getCustomer()->getDefaultShipping()
        ]);
    }

    /**
     * Get customer's recent order
     * @return array|false
     */
    public function getRecentOrderData()
    {
        $collection = $this->orderCollection->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', $this->getCustomer()->getId());

        if ($collection->count()) {
            $order = $collection->getLastItem();
            if ($order) {
                $orderData['date'] = date('F d, Y', time($order->getCreatedAt()));
                $orderData['id'] = $order->getIncrementId();
                $orderData['status'] = ucfirst($order->getStatus());
                $orderData['updated'] = date('F d, Y', time($order->getUpdatedAt()));
                $orderData['url'] = $this->getUrl('sales/order/view', ['order_id' => $order->getId()]);

                return $orderData;
            }
        }

        return false;
    }

    /**
     * Get customer
     * @return \Magento\Customer\Api\Data\CustomerInterface|false
     */
    public function getCustomer()
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->customerSession->getCustomer();
        }
        return false;
    }

    /**
     * Render an address as HTML and return the result
     *
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @return string
     */
    public function getAddressHtml(\Magento\Customer\Api\Data\AddressInterface $address = null)
    {
        if ($address !== null) {
            /** @var \Magento\Customer\Block\Address\Renderer\RendererInterface $renderer */
            $renderer = $this->addressConfig->getFormatByCode('html')->getRenderer();
            return $renderer->renderArray($this->addressMapper->toFlatArray($address));
        }
        return '';
    }

    /**
     * @param int $addressId
     * @return \Magento\Customer\Api\Data\AddressInterface|null
     */
    public function getAddressById($addressId)
    {
        try {
            return $this->addressRepository->getById($addressId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }


    /**
     * add new funtion  dev
     */
    
    /**
     * Get customer data
     * @return array|false
     */
    public function getCustomerData()
    {
        if ($customer = $this->getCustomer()) {
            $customerData['name'] = $customer->getFirstname();
            $customerData['email'] = $customer->getEmail();

            return $customerData;
        }
        return false;
    }

    /**
     * Get customer profile edit url
     * @return string
     */
    public function getCustomerEditUrl()
    {
        return $this->getUrl('customer/account/edit');
    }
}
