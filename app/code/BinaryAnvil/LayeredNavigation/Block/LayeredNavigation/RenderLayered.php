<?php

/**
 * @codingStandardsIgnoreFile
 */

namespace BinaryAnvil\LayeredNavigation\Block\LayeredNavigation;

use Magento\Swatches\Block\LayeredNavigation\RenderLayered as OriginClass;
use Magento\Framework\View\Element\Template\Context;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Catalog\Model\ResourceModel\Layer\Filter\AttributeFactory;
use Magento\Swatches\Helper\Data;
use Magento\Swatches\Helper\Media;
use BinaryAnvil\LayeredNavigation\Model\Url\Builder;
use Magento\Eav\Model\Entity\Attribute\Option;
use Magento\Catalog\Model\Layer\Filter\Item as FilterItem;
use BinaryAnvil\LayeredNavigation\Model\ResourceModel\Attribute\Option\Value;

class RenderLayered extends OriginClass
{

    /** @var Builder $urlBuilder */
    protected $urlBuilder;

    /** @var string Path to template file. */
    protected $_template = 'BinaryAnvil_LayeredNavigation::product/layered/renderer.phtml';

    /**
     * @var \BinaryAnvil\LayeredNavigation\Model\ResourceModel\Attribute\Option\Value $value
     */
    protected $value;

    /** @var array */
    protected $customViewOptions;

    /** @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface */
    protected $productAttributeRepositoryInterface;

    /** @var \Magento\Swatches\Model\ResourceModel\Swatch\Collection */
    protected $swatchCollection;


    /**
     * RenderLayered constructor.
     * @param Context $context
     * @param Attribute $eavAttribute
     * @param AttributeFactory $layerAttribute
     * @param Data $swatchHelper
     * @param Media $mediaHelper
     * @param Builder $urlBuilder
     * @param Value $value
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepositoryInterface
     * @param \Magento\Swatches\Model\ResourceModel\Swatch\Collection $swatchCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Attribute $eavAttribute,
        AttributeFactory $layerAttribute,
        Data $swatchHelper,
        Media $mediaHelper,
        Builder $urlBuilder,
        Value $value,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepositoryInterface,
        \Magento\Swatches\Model\ResourceModel\Swatch\CollectionFactory $swatchCollection,
        array $data = []
    )
    {
        $this->urlBuilder = $urlBuilder;
        $this->value = $value;
        $this->productAttributeRepositoryInterface = $productAttributeRepositoryInterface;
        $this->swatchCollection = $swatchCollection;

        parent::__construct(
            $context,
            $eavAttribute,
            $layerAttribute,
            $swatchHelper,
            $mediaHelper,
            $data
        );
    }


    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSwatchData()
    {
        $this->customViewOptions = $this->value->getAttributeOptionsValues($this->eavAttribute->getAttributeId());
        return parent::getSwatchData();
    }

    /**
     * Build url for filter swatch option
     *
     * @param string $attributeCode
     * @param int $optionId
     * @param bool $isActive
     * @return string
     */
    public function buildOptionUrl($attributeCode, $optionId, $isActive)
    {
        if ($isActive) {
            return $this->urlBuilder->getRemoveFilterUrl($attributeCode, $optionId);
        } else {
            return $this->urlBuilder->getFilterUrl($attributeCode, $optionId);
        }
    }

    /**
     * @param FilterItem $filterItem
     * @param Option $swatchOption
     * @return array
     */
    protected function getOptionViewData(FilterItem $filterItem, Option $swatchOption)
    {
        $customStyle = '';
        $attributeCode = $this->eavAttribute->getAttributeCode();
        $isActive = false;

        if (in_array($filterItem->getValue(), $this->urlBuilder->getValuesFromUrl($attributeCode))) {
            $isActive = true;
        }

        $linkToOption = $this->buildOptionUrl($this->eavAttribute->getAttributeCode(), $filterItem->getValue(), $isActive);
        if ($this->isOptionDisabled($filterItem)) {
            $customStyle = 'disabled';
            $linkToOption = 'javascript:void();';
        }

        return [
            'label' => $swatchOption->getLabel(),
            'link' => $linkToOption,
            'custom_style' => $customStyle,
            'active' => $isActive
        ];
    }

    /**
     * Check if option has custom view
     *
     * @param $optionId
     * @return bool
     */
    public function checkCustomViewOption($optionId)
    {
        if (in_array($optionId, $this->customViewOptions)) {
            return true;
        }
        return false;
    }

    /**
     * Get data for all print filter
     * @return array
     */
    public function getAllPrintLink()
    {
        $attributeCode = $this->eavAttribute->getAttributeCode();
        $isActive = false;

        if (in_array(Value::CUSTOM_VIEW_ATTRIBUTE_OPTIONS_LINK, $this->urlBuilder->getValuesFromUrl($attributeCode))) {
            $isActive = true;
        }

        $linkToOption = $this->buildOptionUrl($attributeCode, Value::CUSTOM_VIEW_ATTRIBUTE_OPTIONS_LINK, $isActive);

        return [
            'label' => Value::CUSTOM_VIEW_ATTRIBUTE_OPTIONS_LABEL,
            'link' => $linkToOption,
            'active' => $isActive
        ];
    }

    /**
     * @param $value
     * @param $array
     * @return array
     */
    public function searchIdByValue($value, $array)
    {
        $result = [];
        foreach ($array as $key => $val) {
            if (in_array($value, $val)) {
                array_push($result, $key);
            }
        }
        return $result;
    }

    /**
     * @param string $key
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOptionsOfColorAttribute($key = 'color')
    {
        $colorArray = [];

        $attribute = $this->productAttributeRepositoryInterface->get('color');

        // Get Store label
        foreach ($attribute->getOptions() as $option) {
            /** @var Magento\Eav\Model\Entity\Attribute\Option $option */
            $name = $option->getLabel();
            $colorId = $option->getValue();

            if ($colorId && $colorId !== '') {
                // Get color code
                $swatchCollection = $this->swatchCollection->create();
                $swatchCollection->addFieldtoFilter('option_id', $colorId);
                /** @var Magento\Swatches\Model\Swatch $item */
                $item = $swatchCollection->getFirstItem();
                $colorCode = $item->getValue();

                // convert colors string to colors array
                $name = trim($name);
                $name = str_replace(" ", "", $name);
                $name = explode(',', $name);

                $colorArray[$colorId] = [
                    'label' => '',
                    'lower_label' => '',
                    "hex" => $colorCode,
                    "list" => $name
                ];
            }
        }

        // Get admin label
        $attribute->setStoreId(0)->getFrontend();
        foreach ($attribute->getOptions() as $option) {
            $colorId = $option->getValue();
            if (isset($colorArray[$colorId])) {
                $label = $option->getLabel();
                $colorArray[$colorId]['lower_label'] = strtolower($label);
                $colorArray[$colorId]['label'] = ucfirst($label);
            }
        }

        return $colorArray;
    }

}
