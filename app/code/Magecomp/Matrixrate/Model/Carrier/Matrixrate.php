<?php
namespace Magecomp\Matrixrate\Model\Carrier;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Psr\Log\LoggerInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magecomp\Matrixrate\Model\ResourceModel\Carrier\MatrixrateFactory;
use Magento\Framework\App\ObjectManager;

class Matrixrate extends AbstractCarrier implements CarrierInterface
{

    protected $_code = 'matrixrate';

    protected $_isFixed = true;

    protected $_defaultConditionName = 'package_weight';

    protected $_conditionNames = [];

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var \Ced\Advancedmatrix\Model\Resource\Carrier\AdvancedrateFactory
     */
    protected $_tablerateFactory;


    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        MatrixrateFactory $tablerateFactory,
        array $data = []
    ) {
    	$this->_scopeConfig = $scopeConfig;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_tablerateFactory = $tablerateFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
		foreach ($this->getCode('condition_name') as $k => $v) {
            $this->_conditionNames[] = $k;
        }

     }

    /**
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result
     */
    public function collectRates(RateRequest $request)
    {
    	
       if (!$this->getConfigFlag('active')) {
           return false;
        }
        
        // exclude Virtual products price from Package value if pre-configured
        if (!$this->getConfigFlag('include_virtual_price') && $request->getAllItems()) {
            foreach ($request->getAllItems() as $item)
			{
                if ($item->getParentItem()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isShipSeparately())
				{
                    foreach ($item->getChildren() as $child) {
                        if ($child->getProduct()->isVirtual() || $item->getProductType() == 'downloadable'){
                            $request->setPackageValue($request->getPackageValue() - $child->getBaseRowTotal());
                        }
                    }
                } elseif ($item->getProduct()->isVirtual() || $item->getProductType() == 'downloadable') {
                    $request->setPackageValue($request->getPackageValue() - $item->getBaseRowTotal());
                }
            }
        }
        
  		// Free shipping by qty
        $freeQty = 0;
        if ($request->getAllItems()) {
            $freePackageValue = 0;
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeShipping = is_numeric($child->getFreeShipping()) ? $child->getFreeShipping() : 0;
                            $freeQty += $item->getQty() * ($child->getQty() - $freeShipping);
                        }
                    }
                } elseif ($item->getFreeShipping()) {
                    $freeShipping = is_numeric($item->getFreeShipping()) ? $item->getFreeShipping() : 0;
                    $freeQty += $item->getQty() - $freeShipping;
                    $freePackageValue += $item->getBaseRowTotal();
                }
            }
            $oldValue = $request->getPackageValue();
            $request->setPackageValue($oldValue - $freePackageValue);
        }
		
		ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r(['set Condition Name' => $request->getConditionMRName()], true));
		
        if (!$request->getConditionMRName()) {
            $conditionName = $this->getConfigData('condition_name');
            $request->setConditionMRName($conditionName ? $conditionName : $this->_defaultConditionName);
        }

        // Package weight and qty free shipping
        $oldWeight = $request->getPackageWeight();
        $oldQty = $request->getPackageQty();

		if ($this->getConfigData('allow_free_shipping_promotions') && !$this->getConfigData('include_free_ship_items')) {
			$request->setPackageWeight($request->getFreeMethodWeight());
			$request->setPackageQty($oldQty - $freeQty);
		}
		
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();
        $zipRange = $this->getConfigData('zip_range');
        $rateArray = $this->getRate($request, $zipRange);

        $request->setPackageWeight($oldWeight);
        $request->setPackageQty($oldQty);

        $foundRates = false;
		$freeShipping=false;
     	
     	if (is_numeric($this->getConfigData('free_shipping_threshold')) && 
	        $this->getConfigData('free_shipping_threshold')>0 &&
	        $request->getPackageValue()>$this->getConfigData('free_shipping_threshold')) {
	         	$freeShipping=true;
	    }
    	if ($this->getConfigData('allow_free_shipping_promotions') &&
	        ($request->getFreeShipping() === true || 
	        $request->getPackageQty() == $this->getFreeBoxes()))
        {
         	$freeShipping=true;
        }
        if ($freeShipping)
        {
		  	$method = $this->_rateMethodFactory->create();
			$method->setCarrier('matrixrate');
			$method->setCarrierTitle($this->getConfigData('title'));
			$method->setMethod('matrixrate_free');
			$method->setPrice('0.00');
			$method->setMethodTitle($this->getConfigData('free_method_text'));
			$result->append($method);
			
			if ($this->getConfigData('show_only_free')) {
				return $result;
			}
		}
		
		ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r(['Rate Array' => $rateArray], true));

        foreach ($rateArray as $rate)
		{
            if (!empty($rate) && $rate['price'] >= 0)
			{
                /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
                $method = $this->_rateMethodFactory->create();

                $method->setCarrier('matrixrate');
                $method->setCarrierTitle($this->getConfigData('title'));

                $method->setMethod('matrixrate_' . $rate['id']);
                $method->setMethodTitle(__($rate['delivery_type']));

                if ($request->getFreeShipping() === true || $request->getPackageQty() == $freeQty) {
                    $shippingPrice = 0;
                } else {
                    $shippingPrice = $this->getFinalPriceWithHandlingFee($rate['price']);
                }

                $method->setPrice($shippingPrice);
                $method->setCost($rate['cost']);

                $result->append($method);
                $foundRates = true; // have found some valid rates
            }
        }

        if (!$foundRates){
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Error $error */
            $error = $this->_rateErrorFactory->create(
                [
                    'data' => [
                        'carrier' => $this->_code,
                        'carrier_title' => $this->getConfigData('title'),
                        'error_message' => $this->getConfigData('specificerrmsg'),
                    ],
                ]
            );
            $result->append($error);
        }

        return $result;
    }

    public function getRate(RateRequest $request,$zipRange)
    {
        return $this->_tablerateFactory->create()->getNewRate($request,$zipRange);
    }

    public function getAllowedMethods()
    {
      return ['matrixrate'=> $this->getConfigData('name')];
    }
	
	public function getCode($type, $code='')
	{
        $codes = array(

            'condition_name'=>array(
                'package_weight' => __('Weight vs. Destination'),
                'package_value'  => __('Price vs. Destination'),
                'package_qty'    => __('# of Items vs. Destination'),
            ),

            'condition_name_short'=>array(
                'package_weight' => __('Weight'),
                'package_value'  => __('Order Subtotal'),
                'package_qty'    => __('# of Items'),
            ),

        );

        if (!isset($codes[$type])) {
            throw new Magento\Framework\Exception\LocalizedException__('Invalid Matrix Rate code type: %1', $type);
        }

        if (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            throw new Magento\Framework\Exception\LocalizedException__('Invalid Matrix Rate code for type %1: %2', $type, $code);
        }

        return $codes[$type][$code];
    }

}
