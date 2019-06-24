<?php
namespace Magecomp\Matrixrate\Model\System\Config\Source\Shipping;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magecomp\Matrixrate\Model\Carrier\MatrixrateFactory;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

class Matrixrate implements \Magento\Framework\Option\ArrayInterface
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
    }


    public function toOptionArray()
    {
		$tableRate = $this->_tablerateFactory->create();
        $arr = [];
        foreach ($tableRate->getCode('condition_name') as $k=>$v) {
            $arr[] = ['value'=>$k, 'label'=>$v];
        }
        return $arr;
    }
}
