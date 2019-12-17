<?php declare(strict_types=1);

namespace Lexim\AbandonedCart\Model;

use Lexim\AbandonedCart\Model\Abandoned;
use Lexim\AbandonedCart\Model\AbandonedFactory;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Model\StoreManagerInterface;

class QuoteRestorer
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var Abandoned
     */
    private $abandonment;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * QuoteRestorer constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param Session $checkoutSession
     * @param AbandonedFactory $abandonmentFactory
     * @param StoreManagerInterface $storeManager
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        Session $checkoutSession,
        AbandonedFactory $abandonmentFactory,
        StoreManagerInterface $storeManager,
        CartRepositoryInterface $cartRepository
    )
    {
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->storeManager = $storeManager;
        $this->cartRepository = $cartRepository;
        $this->abandonment = $abandonmentFactory->create();
    }

    /**
     * @param $quoteId
     */
    public function restore($quoteId)
    {
        $quote = $this->loadQuote($quoteId);
        $this->setupCurrentQuote($quote);
    }

    /**
     * @param int $quoteId
     * @return CartInterface|null
     */
    private function loadQuote($quoteId)
    {
        try {
            /** @var CartInterface $quote */
            $quote = $this->cartRepository->get($quoteId);
        } catch (NoSuchEntityException $e) {
            return null;
        }
        return $quote;
    }

    /**
     * @param CartInterface $quote
     * @return void
     */
    private function setupCurrentQuote($quote)
    {
        if (!$quote->getIsActive()) {
            $quote->setIsActive(1);
            $this->cartRepository->save($quote);
        }
        $this->checkoutSession->replaceQuote($quote);
    }
}
