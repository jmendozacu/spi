<?php
/**
 * Binary Anvil, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Binary Anvil, Inc. Software Agreement
 * that is bundled with this package in the file LICENSE_BAS.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.binaryanvil.com/software/license/
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@binaryanvil.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this software to
 * newer versions in the future. If you wish to customize this software for
 * your needs please refer to http://www.binaryanvil.com/software for more
 * information.
 *
 * @category    BinaryAnvil
 * @package     LayeredNavigation
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

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
    /**
     * @var \BinaryAnvil\LayeredNavigation\Model\Url\Builder $urlBuilder
     */
    protected $urlBuilder;

    /**
     * Path to template file.
     *
     * @var string
     */
    protected $_template = 'BinaryAnvil_LayeredNavigation::product/layered/renderer.phtml';

    /**
     * @var \BinaryAnvil\LayeredNavigation\Model\ResourceModel\Attribute\Option\Value $value
     */
    protected $value;

    /**
     * @var array
     */
    protected $customViewOptions;

    /**
     * RenderLayered constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Eav\Model\Entity\Attribute $eavAttribute
     * @param \Magento\Catalog\Model\ResourceModel\Layer\Filter\AttributeFactory $layerAttribute
     * @param \Magento\Swatches\Helper\Data $swatchHelper
     * @param \Magento\Swatches\Helper\Media $mediaHelper
     * @param \BinaryAnvil\LayeredNavigation\Model\Url\Builder $urlBuilder
     * @param \BinaryAnvil\LayeredNavigation\Model\ResourceModel\Attribute\Option\Value $value
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
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->value = $value;
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
        
        if(in_array($filterItem->getValue(), $this->urlBuilder->getValuesFromUrl($attributeCode))) {
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
     *
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
}
