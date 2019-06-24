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

namespace BinaryAnvil\LayeredNavigation\Plugin\Magento\Catalog\Model\ResourceModel\Eav;

use BinaryAnvil\LayeredNavigation\Model\ResourceModel\Attribute\Option\Value;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as OriginalClass;

class Attribute
{
    /**
     * @var \BinaryAnvil\LayeredNavigation\Model\ResourceModel\Attribute\Option\Value
     */
    protected $value;

    /**
     * Attribute constructor
     *
     * @param \BinaryAnvil\LayeredNavigation\Model\ResourceModel\Attribute\Option\Value $value
     */
    public function __construct(
        Value $value
    ) {
        $this->value = $value;
    }

    /**
     * Save custom view value on attribute saving
     *
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $subject
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $result
     * @return \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(OriginalClass $subject, $result)
    {
        if ($options = $result->getData('customvisual')) {
            $deletedOptions = $result->getOption()['delete'];
            $this->value->removeAttributeValues($result->getAttributeId());
            foreach ($options as $value) {
                if (isset($deletedOptions[$value]) && $deletedOptions[$value]) {
                    continue;
                }
                $this->value->saveOptionValue($result->getAttributeId(), $value);
            }
        }
        return $result;
    }
}
