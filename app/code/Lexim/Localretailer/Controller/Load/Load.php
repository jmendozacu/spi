<?php
namespace Lexim\Localretailer\Controller\Load;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Model\Product;

class Load extends Action
{
    protected $formKey;   
    protected $cart;
    protected $product;
    public function __construct(
        Context $context,
        FormKey $formKey,
        Cart $cart,
        Product $product) {
            $this->formKey = $formKey;
            $this->cart = $cart;
            $this->product = $product;     
            parent::__construct($context);
    }
    public function execute()
    { 
        $storename = $this->getRequest()->getParam('storename');
        $street = $this->getRequest()->getParam('street');
        $city = $this->getRequest()->getParam('city');
        $state = $this->getRequest()->getParam('state');
        $zip = $this->getRequest()->getParam('zip');
        $distance = $this->getRequest()->getParam('distance');
        $phone = $this->getRequest()->getParam('phone');
        $monday = $this->getRequest()->getParam('monday');
        $saturday = $this->getRequest()->getParam('saturday');
        $getidcus = $this->getRequest()->getParam('getidcus');
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('local_retailer_store');

        $sql = "Select * FROM " . $tableName. " WHERE id_customer_use =". $getidcus;
    }
}