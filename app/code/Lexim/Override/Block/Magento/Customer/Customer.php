<?php
/**
 * Lexim Global
 * @author Samuel Kong
 */

namespace Lexim\Override\Block\Magento\Customer;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Http\Context as HttpContext;

use Magento\Checkout\Model\Cart as ModelCart;
use Magento\Customer\Helper\Session\CurrentCustomer;

/**
 * Class Customer
 * @package Lexim\Override\Block\Magento\Customer
 */
class Customer extends \Magento\Customer\Block\Account\Customer
{

    protected $_sessionFactory;

    protected $_cart;

    protected $currentCustomer;


    /**
     * Customer constructor.
     * @param Context $context
     * @param HttpContext $httpContext
     * @param CurrentCustomer $currentCustomer
     * @param ModelCart $cart
     * @param array $data
     */
    public function __construct(
        Context $context,
        HttpContext $httpContext,
        CurrentCustomer $currentCustomer,
        ModelCart $cart,
        array $data = []
    )
    {
        $this->_cart = $cart;
        $this->currentCustomer = $currentCustomer;
        parent::__construct($context, $httpContext, $data);
    }

    /**
     * Get sub total from cart
     * @return float
     */
    function getSubTotal()
    {
        return $this->_cart->getQuote()->getSubtotal();
    }


    function getCurrentCustomer() {
        return $this->currentCustomer->getCustomer();
    }

}