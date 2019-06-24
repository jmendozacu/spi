<?php
namespace Magecomp\Matrixrate\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'advanced_matrixrate'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('shipping_matrixrate')
        )->addColumn('id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'website_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Website Id'
        )->addColumn(
            'dest_country_id',
            Table::TYPE_TEXT,
            4,
            ['nullable' => false, 'default' => '0'],
            'Destination coutry ISO/2 or ISO/3 code'
        )->addColumn(
            'dest_region_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Destination Region Id'
        )->addColumn(
            'dest_city',
            Table::TYPE_TEXT,
            30,
            ['nullable' => false, 'default' => ''],
            'Destination City'
        )->addColumn(
            'dest_zip',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Destination Post Code (Zip)'
        )->addColumn(
            'dest_zip_to',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Destination Post Code To (Zip)'
        )->addColumn(
            'condition_name',
            Table::TYPE_TEXT,
            20,
            ['nullable' => false, 'default' => ''],
            'Destination Post Code To (Zip)'
        )->addColumn(
            'condition_from_value',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Condition From'
        )->addColumn(
            'condition_to_value',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Condition To'
        )->addColumn(
            'price',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Price'
        )->addColumn(
            'cost',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Cost'
        )->addColumn(
            'delivery_type',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Delivery Type'
        )->addIndex(
            $installer->getIdxName(
                'shipping_matrixrate',
                ['website_id','dest_country_id','dest_region_id','dest_city','dest_zip','dest_zip_to','condition_name','condition_from_value','condition_to_value','delivery_type'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['website_id','dest_country_id','dest_region_id','dest_city','dest_zip','dest_zip_to','condition_name','condition_from_value','condition_to_value','delivery_type'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->setComment(
            'Shipping Matrix Rate'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();

    }
}