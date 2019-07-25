<?php
namespace BinaryAnvil\Customer\Controller\Load;
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
        $name = $this->getRequest()->getParam('name');
        $lastname = $this->getRequest()->getParam('lastname');
        $address = $this->getRequest()->getParam('address');
        $city = $this->getRequest()->getParam('city');
        $phone = $this->getRequest()->getParam('phone');
        $zipcode = $this->getRequest()->getParam('zipcode');
        $state = $this->getRequest()->getParam('state');
        $country = $this->getRequest()->getParam('country');
        $customer_id = $this->getRequest()->getParam('customer_id');
        
        $set_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $object_addres = $set_objectManager->get('\Magento\Customer\Model\AddressFactory');
        $set_address = $object_addres->create();
      
        $set_address->setCustomerId($customer_id)
        ->setFirstname($name)
        ->setLastname($lastname)
        ->setCountryId($country)
        // if Customer country is USA then need add state / province 
        ->setRegionId($state) 
        ->setPostcode($zipcode)
        ->setCity($city)
        ->setTelephone($phone)
        // ->setCompany('GMI')
        ->setStreet($address)
        // ->setIsDefaultBilling('1')
        ->setIsDefaultShipping('1')
        ->setSaveInAddressBook('1');
        try{
            $set_address->save();
                    // save Customer address
        }
        catch (Exception $exception) {
            Zend_Debug::dump($exception->getMessage());
    }
     }
}