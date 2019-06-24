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

namespace BinaryAnvil\LayeredNavigation\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use BinaryAnvil\LayeredNavigation\Model\ResourceModel\Attribute\Option\Value;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @throws \Zend_Db_Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->createAttributeOptionTable($setup);
        }

        $setup->endSetup();
    }

    /**
     * Create 'binaryanvil_eav_attribute_option_value' table
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @throws \Zend_Db_Exception
     * @return void
     */
    public function createAttributeOptionTable(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()->newTable(
            $installer->getTable(Value::DB_SCHEMA_TABLE_EAV_ATTRIBUTE_OPTION_VALUE)
        )->addColumn(
            Value::DB_SCHEMA_COLUMN_VALUE_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Value ID'
        )->addColumn(
            Value::DB_SCHEMA_COLUMN_ATTRIBUTE_ID,
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Attribute Id'
        )->addColumn(
            Value::DB_SCHEMA_COLUMN_OPTION_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Option Id'
        )->addForeignKey(
            $installer->getFkName(
                $setup->getTable(Value::DB_SCHEMA_TABLE_EAV_ATTRIBUTE_OPTION_VALUE),
                Value::DB_SCHEMA_COLUMN_ATTRIBUTE_ID,
                $setup->getTable('catalog_eav_attribute'),
                'attribute_id'
            ),
            Value::DB_SCHEMA_COLUMN_ATTRIBUTE_ID,
            $setup->getTable('catalog_eav_attribute'),
            'attribute_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                $setup->getTable(Value::DB_SCHEMA_TABLE_EAV_ATTRIBUTE_OPTION_VALUE),
                Value::DB_SCHEMA_COLUMN_OPTION_ID,
                $setup->getTable('eav_attribute_option'),
                'option_id'
            ),
            Value::DB_SCHEMA_COLUMN_OPTION_ID,
            $setup->getTable('eav_attribute_option'),
            'option_id',
            Table::ACTION_CASCADE
        )->addIndex(
            $installer->getIdxName(
                $installer->getTable(Value::DB_SCHEMA_TABLE_EAV_ATTRIBUTE_OPTION_VALUE),
                [Value::DB_SCHEMA_COLUMN_OPTION_ID],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [Value::DB_SCHEMA_COLUMN_OPTION_ID],
            AdapterInterface::INDEX_TYPE_UNIQUE
        );

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
