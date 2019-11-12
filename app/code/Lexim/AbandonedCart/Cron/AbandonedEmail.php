<?php declare(strict_types=1);

namespace Lexim\AbandonedCart\Cron;

use Lexim\AbandonedCart\Model\Abandoned;
use Lexim\AbandonedCart\Model\AbandonedFactory;
use Lexim\AbandonedCart\Model\ResourceModel\Abandoned\CollectionFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class AbandonedEmail
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Abandoned
     */
    private $abandoned;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * AbandonedEmail constructor.
     * @param StoreManagerInterface $storeManager
     * @param CartRepositoryInterface $cartRepository
     * @param CollectionFactory $collectionFactory
     * @param AbandonedFactory $abandonedFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CartRepositoryInterface $cartRepository,
        CollectionFactory $collectionFactory,
        AbandonedFactory $abandonedFactory
    ) {
        $this->storeManager = $storeManager;
        $this->cartRepository = $cartRepository;
        $this->abandoned = $abandonedFactory->create();
        $this->collectionFactory = $collectionFactory;
    }


    public function abandonedCarts()
    {
        //Send email
        //We need to check the
        $collection = $this->collectionFactory->create();
        foreach ($collection->getItems() as $item) {
            /** @var Abandoned $item */
            $quoteId = $item->getQuoteId();
            $quote = $this->cartRepository->get($quoteId);
            if ($quote->getIsActive()) {
              // Set email

            } else {
                //Delete inactive model
            }
        }
    }
}
