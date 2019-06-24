<?php
namespace Magecomp\Matrixrate\Model\System\Config\Backend\Shipping;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magecomp\Matrixrate\Model\ResourceModel\Carrier\MatrixrateFactory;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

class Matrixrate extends \Magento\Framework\App\Config\Value
{
	protected $_tablerateFactory;

	public function __construct(
    		Context $context,
    		Registry $registry,
    		ScopeConfigInterface $config,
    		TypeListInterface $cacheTypeList,
    		MatrixrateFactory $tablerateFactory,
    		AbstractResource $resource = null,
    		AbstractDb $resourceCollection = null,
    		array $data = []
    ) {
    	$this->_tablerateFactory = $tablerateFactory;
    	parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    public function afterSave()
    {
        $tableRate = $this->_tablerateFactory->create();
        $tableRate->uploadAndImport($this);
        return parent::afterSave();
    }
}

