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
        $retailerid = $this->getRequest()->getParam('retailerid');
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
        if(isset($getidcus)){
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $tableName = $resource->getTableName('local_retailer_store');

            $storename = str_replace("'", "''", $storename);
            $street = str_replace("'", "''", $street); 

            $sql = "Select * FROM " . $tableName. " WHERE id_customer_use =". $getidcus;
            $array_customer = $connection->fetchAll($sql);
            if (!empty($array_customer)){
                $sql_update = " update " . $tableName . " SET retailer_id = '".$retailerid."', store_name = '".$storename."', street = '".$street."', city = '".$city."', state = '".$state."', zip = '".$zip."', distance = '".$distance."', phone = '".$phone."', monday = '".$monday."', saturday = '".$saturday."' WHERE id_customer_use = " . $getidcus;
                $connection->query($sql_update);
            }else{
                $sql_update = "Insert Into " . $tableName . " (id, retailer_id, store_name, street, city, state, zip, distance, phone, monday, saturday, id_customer_use) Values ('','".$retailerid."','".$storename."','".$street."','".$city."','".$state."','".$zip."','".$distance."','".$phone."','".$monday."','".$saturday."','".$getidcus."')";
                $connection->query($sql_update); 
            }
        }
    }
}