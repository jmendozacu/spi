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

class Greeting extends Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        SessionFactory $customerSession,
        array $data = []
    ) {
        $this->customerSession = $customerSession->create();
        parent::__construct($context, $data);
    }

    /**
     * Get customer data
     * @return array|false
     */
    public function getCustomerData()
    {
        if ($customer = $this->getCustomer()) {
            $customerData['name'] = $customer->getFirstname();
            $customerData['nickname'] = $customer->getNickname();
            $customerData['email'] = $customer->getEmail();

            return $customerData;
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
     * Get customer profile edit url
     * @return string
     */
    public function getCustomerEditUrl()
    {
        return $this->getUrl('customer/account/edit');
    }
}
