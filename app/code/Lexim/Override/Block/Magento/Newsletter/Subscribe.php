<?php
/**
 * Lexim Global
 * @author Samuel Kong
 */

namespace Lexim\Override\Block\Magento\Newsletter;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\SessionFactory;

/**
 * Class Subscribe
 * @package Lexim\Override\Block\Magento\Newsletter
 */
class Subscribe extends \Magento\Newsletter\Block\Subscribe
{

    /**
     * @var SessionFactory
     */
    protected $_sessionFactory;


    /**
     * Subscribe constructor.
     * @param Template\Context $context
     * @param SessionFactory $sessionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        SessionFactory $sessionFactory,
        array $data = []
    ){
        $this->_sessionFactory = $sessionFactory->create();
        parent::__construct($context, $data);
    }


    /**
     * Check user is loggedIn
     * @return bool
     */
    public function isCustomerLoggedIn() {
        return $this->_sessionFactory->isLoggedIn();
    }



}