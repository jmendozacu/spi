<?php
/**
 * Lexim Global
 * @author Samuel Kong
 */

namespace Lexim\Override\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Url\Helper\Data;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProTypeModel;

/**
 * Class ListProduct
 * @package Lexim\Override\Block
 */
class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    private $_configurableProTypeModel;


    /**
     * ListProduct constructor.
     * @param Context $context
     * @param PostHelper $postDataHelper
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Data $urlHelper
     * @param ConfigurableProTypeModel $configurableProTypeModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        ConfigurableProTypeModel $configurableProTypeModel,
        array $data = []
    )
    {
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
        $this->_configurableProTypeModel = $configurableProTypeModel;
    }

    /**
     * @param $product
     * @param bool $paramList
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getAssociatedProductsByConfigurableProduct($product, $paramList = false)
    {
        if (!$paramList || !$product) {
            return null;
        }

        if ($product->getTypeId() !== "configurable") {
            return null;
        }

        // test
        //$paramList = [
        //    'is_manufacturer_color' => [47, 91, 86, 60, 45, 74],
        //    'is_size' => '',
        //    'is_apparel_rise_length' => ''
        //];

        //get configurable products attributes array with all values with label (supper attribute which use for configuration)
        $optionsData = $product->getTypeInstance()->getConfigurableAttributesAsArray($product);
        $attributeValues = [];

        // prepare array with attribute values
        if ($optionsData && is_array($optionsData)) {
            foreach ($optionsData as $option) {
                $attrCode = $option['attribute_code'];
                $attrId = $option['attribute_id'];

                if ($attrCode && isset($paramList[$attrCode])) {
                    $attributeValues[$attrId] = $paramList[$attrCode];
                }
            }
            return $this->_configurableProTypeModel->getProductByAttributes($attributeValues, $product);
        }
        return null;
    }

}