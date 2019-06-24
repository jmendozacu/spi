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
 * @category BinaryAnvil
 * @package SizeChart
 * @copyright Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license http://www.binaryanvil.com/software/license
 */
namespace BinaryAnvil\SizeChart\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Catalog\Setup\CategorySetup;

class Attributes implements ArrayInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    protected $attribute;

    /**
     * Attributes constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
     */
    public function __construct(Attribute $attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [];
            $attributeCollection = $this->attribute->getCollection()
                ->addFieldToFilter(AttributeInterface::ENTITY_TYPE_ID, CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID);
            foreach ($attributeCollection as $attribute) {
                $code = $attribute->getAttributeCode();
                $label = $attribute->getFrontendLabel();
                if ($code) {
                    $this->options[] = ['value' => $code, 'label' => $label];
                }
            }
        }
        return $this->options;
    }
}
