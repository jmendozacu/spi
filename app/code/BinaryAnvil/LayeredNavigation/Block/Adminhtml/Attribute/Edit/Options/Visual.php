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

// @codingStandardsIgnoreFile

namespace BinaryAnvil\LayeredNavigation\Block\Adminhtml\Attribute\Edit\Options;

use Magento\Swatches\Block\Adminhtml\Attribute\Edit\Options\Visual as OriginClass;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory;
use Magento\Framework\Validator\UniversalFactory;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Swatches\Helper\Media;
use BinaryAnvil\LayeredNavigation\Model\ResourceModel\Attribute\Option\Value as Value;

class Visual extends OriginClass
{
    /**
     * @var string
     */
    protected $_template = 'BinaryAnvil_LayeredNavigation::catalog/product/attribute/visual.phtml';

    /**
     * @var \BinaryAnvil\LayeredNavigation\Model\ResourceModel\Attribute\Option\Value $value
     */
    protected $value;

    /**
     * @var array
     */
    protected $customOptionView;

    /**
     * Visual constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Catalog\Model\Product\Media\Config $mediaConfig
     * @param \Magento\Swatches\Helper\Media $swatchHelper
     * @param \BinaryAnvil\LayeredNavigation\Model\ResourceModel\Attribute\Option\Value $value
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CollectionFactory $attrOptionCollectionFactory,
        UniversalFactory $universalFactory,
        Config $mediaConfig,
        Media $swatchHelper,
        Value $value,
        array $data
    ) {
        $this->value = $value;
        parent::__construct(
            $context,
            $registry,
            $attrOptionCollectionFactory,
            $universalFactory,
            $mediaConfig,
            $swatchHelper,
            $data
        );
    }
    
    /**
     * Return json config for visual option JS initialization
     *
     * @return array
     * @since 100.1.0
     */
    public function getJsonConfig()
    {
        $values = [];
        $this->customOptionView = $this->value->getAttributeOptionsValues($this->getAttributeObject()->getAttributeId());
        foreach ($this->getOptionValues() as $value) {
            $value = $this->addCustomViewValues($value);
            $values[] = $value->getData();
        }

        $data = [
            'attributesData' => $values,
            'uploadActionUrl' => $this->getUrl('swatches/iframe/show'),
            'isSortable' => (int)(!$this->getReadOnly() && !$this->canManageOptionDefaultOnly()),
            'isReadOnly' => (int)$this->getReadOnly()
        ];

        return json_encode($data);
    }

    /**
     * Add custom view param value
     *
     * @param $value
     * @return mixed
     */
    public function addCustomViewValues($value)
    {
        $value->setData('customView', 'checkbox');
        if (in_array($value->getId(), $this->customOptionView)) {
            $value->setData('customViewChecked', 'checked');
        }
        return $value;
    }
}
