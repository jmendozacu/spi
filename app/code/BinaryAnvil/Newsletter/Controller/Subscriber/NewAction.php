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
 * @package     Newsletter
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */
namespace BinaryAnvil\Newsletter\Controller\Subscriber;

use Magento\Newsletter\Controller\Subscriber\NewAction as OriginClass;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Customer\Model\Session;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Url;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Newsletter\Model\Subscriber;
use Magento\Framework\Exception\LocalizedException;

class NewAction extends OriginClass
{
    /**
     * @var \Magento\Framework\Controller\ResultFactory $resultJsonFactory
     */
    protected $resultJsonFactory;

    /**
     * NewAction constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement
     * @param \Magento\Framework\Controller\ResultFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        SubscriberFactory $subscriberFactory,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        Url $customerUrl,
        AccountManagementInterface $customerAccountManagement,
        ResultFactory $resultJsonFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct(
            $context,
            $subscriberFactory,
            $customerSession,
            $storeManager,
            $customerUrl,
            $customerAccountManagement
        );
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $email = false;
        $success = false;
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $email = (string)$this->getRequest()->getPost('email');

            try {
                $this->validateEmailFormat($email);
                $this->validateGuestSubscription();
                $this->validateEmailAvailable($email);

                $subscriber = $this->_subscriberFactory->create()->loadByEmail($email);
                if ($subscriber->getId()
                    && $subscriber->getSubscriberStatus() == Subscriber::STATUS_SUBSCRIBED
                ) {
                    throw new LocalizedException(
                        __('This email address is already subscribed.')
                    );
                }

                $this->_subscriberFactory->create()->subscribe($email);
                $success = true;
            } catch (LocalizedException $e) {
                $this->messageManager->addException(
                    $e,
                    __('There was a problem with the subscription: %1', $e->getMessage())
                );
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong with the subscription.'));
            }
        }

        $result = $this->resultJsonFactory->create(ResultFactory::TYPE_JSON);
        $result->setData(['email' => $email, 'success' => $success]);
        return $result;
    }
}
