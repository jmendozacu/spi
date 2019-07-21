<?php

namespace Aheadworks\OneStepCheckout\Controller\Index;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;


/**
 * Class SaveQuote
 * @package Aheadworks\OneStepCheckout\Controller\Index
 */
class SaveQuote extends Action
{

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;


    /**
     * SaveQuote constructor.
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession
    )
    {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
    }


    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Exception
     */
    public function execute()
    {
        // From saveEmailToQuote function in email.js
        $postData = $this->getRequest()->getPostValue();
        $quote = $this->checkoutSession->getQuote();
        $save = false;

        if (isset($postData["customerEmail"]) && $postData["customerEmail"] != "") {
            $quote->setCustomerEmail($postData["customerEmail"]);
            $save = true;
        }

        if (isset($postData["firstName"]) && $postData["firstName"] != "") {
            $quote->setCustomerFirstname($postData["firstName"]);
            $save = true;
        }

        if (isset($postData["lastName"]) && $postData["lastName"] != "") {
            $quote->setCustomerLastname($postData["lastName"]);
            $save = true;
        }

        if ($save) {
            $quote->save();
        }

        echo json_encode($postData);
    }
}
