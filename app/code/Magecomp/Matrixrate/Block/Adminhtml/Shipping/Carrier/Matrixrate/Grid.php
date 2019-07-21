<?php
namespace Magecomp\Matrixrate\Block\Adminhtml\Shipping\Carrier\Matrixrate;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as BackendHelperData;
use Magecomp\Matrixrate\Model\ResourceModel\Carrier\Matrixrate\CollectionFactory;
use Magecomp\Matrixrate\Model\Carrier\Matrixrate;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
	protected $_websiteId;

    protected $_matrixrate;

    protected $_collectionFactory;
    
    protected $_conditionName;
	
	public function __construct(
        Context $context,
        BackendHelperData $backendHelper,
        CollectionFactory $collectionFactory,
        Matrixrate $matrixrate,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_matrixrate = $matrixrate;
        parent::__construct($context, $backendHelper, $data);
    }
	
	protected function _construct()
	{
        parent::_construct();
        $this->setId('shippingMatrixrateGrid');
        $this->_exportPageSize = 10000;
    }
	
	protected function _prepareCollection()
	{
        $collection = $this->_collectionFactory->create();
        $collection->setConditionFilter($this->getConditionName())
        ->setWebsiteFilter($this->getWebsiteId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
	}
	
  /**
     * Prepare table columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('dest_country', array(
            'header'    => __('Country'),
            'index'     => 'dest_country',
            'default'   => '*',
        ));

        $this->addColumn('dest_region', array(
            'header'    => __('Region/State'),
            'index'     => 'dest_region',
            'default'   => '*',
        ));
        
        $this->addColumn('dest_city', array(
            'header'    => __('City'),
            'index'     => 'dest_city',
            'default'   => '*',
        ));

        $this->addColumn('dest_zip', array(
            'header'    => __('Zip/Postal Code From'),
            'index'     => 'dest_zip',
			'default'   => '*',
        ));

        $this->addColumn('dest_zip_to', array(
            'header'    => __('Zip/Postal Code To'),
            'index'     => 'dest_zip_to',
			'default'   => '*',
		));
        
        
        $label = $this->_matrixrate->getCode('condition_name_short', $this->getConditionName());

        $this->addColumn('condition_from_value', array(
            'header'    => $label.' From',
            'index'     => 'condition_from_value',
        ));

        $this->addColumn('condition_to_value', array(
            'header'    => $label.' To',
            'index'     => 'condition_to_value',
        ));

        $this->addColumn('price', array(
            'header'    => __('Shipping Price'),
            'index'     => 'price',
        ));
        
        $this->addColumn('delivery_type', array(
            'header'    => __('Delivery Type'),
            'index'     => 'delivery_type',
        ));

        return parent::_prepareColumns();
    }
    
   	public function setWebsiteId($websiteId)
    {
        $this->_websiteId = $this->_storeManager->getWebsite($websiteId)->getId();
        return $this;
    }
	
	/**
     * Retrieve current website id
     *
     * @return int
     */
    public function getWebsiteId()
    {
        if ($this->_websiteId === null) {
            $this->_websiteId = $this->_storeManager->getWebsite()->getId();
        }
        return $this->_websiteId;
    }
	public function getConditionName()
	{
    	return $this->_conditionName;
    }
    
    public function setConditionName($name)
    {
    	$this->_conditionName = $name;
    	return $this;
    }
	
}