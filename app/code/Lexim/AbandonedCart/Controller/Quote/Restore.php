<?php declare(strict_types=1);

namespace Lexim\AbandonedCart\Controller\Quote;

use Lexim\AbandonedCart\Model\QuoteRestorer;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Restore extends Action
{
    /**
     * @var QuoteRestorer
     */
    private $quoteRestorer;

    /**
     * Restore constructor.
     * @param QuoteRestorer $quoteRestorer
     * @param Context $context
     */
    public function __construct(
        QuoteRestorer $quoteRestorer,
        Context $context
    )
    {
        $this->quoteRestorer = $quoteRestorer;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        //For example: cart_abandonment/quote/restore?quote_id=345
        $quoteId = $this->getRequest()->getParam('quote_id');

        //no quote id redirect to base url
        if (!$quoteId) {
            return $resultRedirect->setPath('checkout/cart');
        }
        try {
            $this->quoteRestorer->restore($quoteId);
            $this->messageManager->addSuccessMessage(__('You shopping cart has been restored successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('An error occurred while restoring the quote.'));
        }
        return $resultRedirect->setPath('checkout/cart');
    }
}
