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

namespace BinaryAnvil\LayeredNavigation\Model\ResourceModel\Attribute\Option;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Value extends AbstractDb
{
    /**
     * Custom view attr options table
     */
    const DB_SCHEMA_TABLE_EAV_ATTRIBUTE_OPTION_VALUE = 'binaryanvil_eav_attribute_option_value';

    /**#@+
     * Constants for table's column names
     */
    const DB_SCHEMA_COLUMN_VALUE_ID     = 'value_id';
    const DB_SCHEMA_COLUMN_ATTRIBUTE_ID    = 'attribute_id';
    const DB_SCHEMA_COLUMN_OPTION_ID    = 'option_id';
    /**#@-*/

    /**
     * Custom view attr options table
     */
    const CUSTOM_VIEW_ATTRIBUTE_OPTIONS_LINK = 'all_print';
    const CUSTOM_VIEW_ATTRIBUTE_OPTIONS_LABEL = 'All Prints';
    /**#@-*/

    // @codingStandardsIgnoreStart
    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::DB_SCHEMA_TABLE_EAV_ATTRIBUTE_OPTION_VALUE, self::DB_SCHEMA_COLUMN_VALUE_ID);
    }
    // @codingStandardsIgnoreEnd

    /**
     * Get attribute value by option ID
     *
     * @param int $optionId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOptionValue($optionId)
    {
        $connection = $this->getConnection();
        if ($data = $connection->fetchCol(
            $connection->select()->from(
                ['op' => $this->getMainTable()],
                [self::DB_SCHEMA_COLUMN_OPTION_ID]
            )->where(
                'op.' . self::DB_SCHEMA_COLUMN_OPTION_ID . ' = ?',
                $optionId
            )->limit(
                1
            )
        )) {
            return !empty($data) ? $data[0] : '';
        }
        return '';
    }

    /**
     * Get options value by attribute ID
     *
     * @param $attributeId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttributeOptionsValues($attributeId)
    {
        $connection = $this->getConnection();
        if ($data = $connection->fetchCol(
            $connection->select()->from(
                ['op' => $this->getMainTable()],
                [self::DB_SCHEMA_COLUMN_OPTION_ID]
            )->where(
                'op.' . self::DB_SCHEMA_COLUMN_ATTRIBUTE_ID . ' = ?',
                $attributeId
            )
        )) {
            return !empty($data) ? $data : [];
        }
        return [];
    }

    /**
     * Save attribute option value.
     *
     * @param int $attributeId
     * @param int $optionId
     * @return $this
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function saveOptionValue($attributeId, $optionId)
    {
        $data = [
            [
                self::DB_SCHEMA_COLUMN_ATTRIBUTE_ID => $attributeId,
                self::DB_SCHEMA_COLUMN_OPTION_ID => $optionId
            ]
        ];
        try {
            $this->removeOptionValue($optionId);
            $this->getConnection()->insertOnDuplicate(
                $this->getMainTable(),
                $data,
                array_keys($data)
            );
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('There was an error saving attribute option.'));
        }
        return $this;
    }

    /**
     * Remove attribute custom option value
     *
     * @param $optionId
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function removeOptionValue($optionId)
    {
        if (!$optionId) {
            throw new LocalizedException(__('Option ID doesn\'t specified.'));
        }
        $this->getConnection()->delete(
            $this->getTable(self::DB_SCHEMA_TABLE_EAV_ATTRIBUTE_OPTION_VALUE),
            [
                self::DB_SCHEMA_COLUMN_OPTION_ID . ' = ?' => $optionId
            ]
        );
        return $this;
    }

    /**
     * Remove attribute custom option value
     *
     * @param $attributeId
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function removeAttributeValues($attributeId)
    {
        if (!$attributeId) {
            throw new LocalizedException(__('Attribute ID doesn\'t specified.'));
        }
        $this->getConnection()->delete(
            $this->getTable(self::DB_SCHEMA_TABLE_EAV_ATTRIBUTE_OPTION_VALUE),
            [
                self::DB_SCHEMA_COLUMN_ATTRIBUTE_ID . ' = ?' => $attributeId
            ]
        );
        return $this;
    }
}
