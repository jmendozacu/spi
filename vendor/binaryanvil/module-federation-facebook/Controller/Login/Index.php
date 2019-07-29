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
 * @package     BinaryAnvil_FederationFacebook
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\FederationFacebook\Controller\Login;

use BinaryAnvil\FederationFacebook\Api\CustomerRepositoryInterface;
use BinaryAnvil\FederationFacebook\Model\Facebook\Config;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Api\AccountManagementInterface;
use BinaryAnvil\FederationFacebook\Model\Facebook;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\InputException;
use Facebook\Exceptions\FacebookSDKException;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Action\Context;
use BinaryAnvil\Federation\Logger\Logger;
use Magento\Framework\App\Action\Action;
use Facebook\Authentication\AccessToken;
use Magento\Customer\Model\Session;
use Facebook\GraphNodes\GraphUser;
use Exception;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Index extends Action
{
    /**
     * @codingStandardsIgnoreStart
     */
    /**
     * @var string $_msgConnected
     */
    private $_msgConnected = 'Your Facebook account is now connected to your store account.';

    /**
     * @var string $_msgLoggedIn
     */
    private $_msgLoggedIn = 'You have successfully logged in using your Facebook account.';

    /**
     * @var string $_msgRegister
     */
    private $_msgRegister = 'Your new store account has been created.';

    /**
     * @var string $_msgRegistered
     */
    private $_msgRegistered = 'You already have an account with us.';
    /**
     * @codingStandardsIgnoreEnd
     */

    /**
     * @var \BinaryAnvil\FederationFacebook\Model\Facebook $facebook
     */
    private $facebook;

    /**
     * @var \Magento\Customer\Model\Session $session
     */
    private $session;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterface $customer
     */
    private $customer;

    /**
     * @var \BinaryAnvil\FederationFacebook\Api\CustomerRepositoryInterface $customerRepository
     */
    private $customerRepository;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface $accountManagement
     */
    private $accountManagement;

    /**
     * @var \BinaryAnvil\Federation\Logger\Logger $logger
     */
    private $logger;

    /**
     * @var \BinaryAnvil\FederationFacebook\Model\Facebook\Config $config
     */
    private $config;

    /**
     * @param \BinaryAnvil\FederationFacebook\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Api\AccountManagementInterface $accountManagement
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param \BinaryAnvil\FederationFacebook\Model\Facebook $facebook
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \BinaryAnvil\Federation\Logger\Logger $logger
     * @param \BinaryAnvil\FederationFacebook\Model\Facebook\Config $config
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        CustomerInterface $customer,
        Facebook $facebook,
        Context $context,
        Session $session,
        Logger $logger,
        Config $config
    ) {
        $this->customerRepository = $customerRepository;
        $this->accountManagement = $accountManagement;
        $this->facebook = $facebook;
        $this->customer = $customer;
        $this->session = $session;
        $this->logger = $logger;
        $this->config = $config;

        parent::__construct($context);
    }

    /**
     * Authorize customer
     *
     * @param int $customerId
     * @return void
     */
    private function login($customerId)
    {
        $this->session->loginById($customerId);
        $this->session->regenerateId();
    }

    /**
     * Upsert user and login
     *
     * @param \Facebook\GraphNodes\GraphUser $facebookUser
     * @param \Facebook\Authentication\AccessToken $accessToken
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    private function upsert(GraphUser $facebookUser, AccessToken $accessToken)
    {
        if (!$this->customer->getId()) {
            $this->customer->setEmail($facebookUser->getEmail());
            $this->customer->setFirstname($facebookUser->getFirstName());
            $this->customer->setLastname($facebookUser->getLastName());
            $this->customer->setMiddlename($facebookUser->getMiddleName());
            $this->customer->setGender(
                (int)($facebookUser->getGender() == 'male')
            );
        }

        $this->customer->setCustomAttribute(Config::ATTR_FACEBOOK_ID, $facebookUser->getId());
        $this->customer->setCustomAttribute(Config::ATTR_FACEBOOK_TOKEN, serialize($accessToken));

        if ($this->customer->getId()) {
            $customer = $this->customerRepository->save($this->customer);
        } else {
            $customer = $this->accountManagement->createAccount($this->customer);
        }

        $this->login($customer->getId());

        return $customer;
    }

    /**
     * Dispatch request
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        /** @var \Facebook\Helpers\FacebookRedirectLoginHelper $loginHelper */
        $loginHelper = $this->facebook->getRedirectLoginHelper();

        try {
            /** @var \Facebook\Authentication\AccessToken $accessToken */
            $accessToken = $loginHelper->getAccessToken();

            if (isset($accessToken)) {

                /** @var \Facebook\GraphNodes\GraphUser $facebookUser */
                $facebookUser = $this->facebook->get('/me?fields=' . Config::FACEBOOK_FIELDS, $accessToken)
                    ->getGraphUser();

                /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
                $customer = $this->customerRepository->getByFacebookId(
                    $facebookUser->getId()
                );

                if ($this->session->getId()) {
                    $this->customer = $this->session->getCustomerData();
                    $this->upsert($facebookUser, $accessToken);

                    $this->messageManager->addSuccessMessage(__($this->_msgConnected));
                } else {
                    if ($customer != null) {
                        $this->customer = $customer;
                        $this->upsert($facebookUser, $accessToken);

                        $this->messageManager->addSuccessMessage(__($this->_msgLoggedIn));
                    } else {
                        try {
                            /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
                            $this->customer = $this->customerRepository->get($facebookUser->getEmail());
                        } finally {
                            /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
                            $customer = $this->upsert($facebookUser, $accessToken);

                            if ($this->customer->getId() == $customer->getId()) {
                                $this->messageManager->addSuccessMessage(
                                    __($this->_msgRegistered . ' ' . $this->_msgConnected)
                                );
                            } else {
                                $this->messageManager->addSuccessMessage(
                                    __($this->_msgRegister . ' ' . $this->_msgConnected)
                                );
                            }
                        }
                    }
                }
            } else {
                throw new FacebookSDKException('The Facebook code is null.');
            }
        } catch (FacebookSDKException $e) {
            $this->logger->addError($e->getMessage());

            $this->messageManager->addErrorMessage(__(
                "Oops. Something went wrong! Please try again later."
            ));
        } catch (InputException $e) {
            $this->logger->addError($e->getMessage());

            $this->messageManager->addErrorMessage(__(
                "Some of required values is not received. Please, check your Facebook settings."
                . "Required fields: email, first name, last name."
            ));
        } catch (NoSuchEntityException $e) {
            $this->logger->addInfo($e->getMessage());
        } catch (Exception $e) {
            $this->logger->addError($e->getMessage());

            $this->messageManager->addErrorMessage(__(
                "Oops. Something went wrong! Please try again later."
            ));
        }

        return $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->config->isEnabled()) {
            $this->_redirect($this->_redirect->getRefererUrl());
        }

        return parent::dispatch($request);
    }
}
