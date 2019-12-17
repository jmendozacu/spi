<?php declare(strict_types=1);

namespace Lexim\AbandonedCart\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $this->createAbandonedCartTable($installer);
        $installer->endSetup();
    }

    /**
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function createAbandonedCartTable($installer)
    {
        $tableName = $installer->getTable('lx_email_abandoned_cart');
        $this->dropTableIfExists($installer, $tableName);
        $abandonedCartTable = $installer->getConnection()->newTable($installer->getTable($tableName));
        $abandonedCartTable = $this->addColumnForAbandonedCartTable($abandonedCartTable);
        $abandonedCartTable = $this->addIndexKeyForAbandonedCarts($installer, $abandonedCartTable);
        $abandonedCartTable->setComment('Lexim Abandoned Carts Table');
        $installer->getConnection()->createTable($abandonedCartTable);
    }

    /**
     * @param SchemaSetupInterface $installer
     * @param string $table
     */
    private function dropTableIfExists($installer, $table)
    {
        if ($installer->getConnection()->isTableExists($installer->getTable($table))) {
            $installer->getConnection()->dropTable(
                $installer->getTable($table)
            );
        }
    }

    /**
     * @param $table
     * @return mixed
     */
    private function addColumnForAbandonedCartTable($table)
    {
        return $table->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            [
                'primary' => true,
                'identity' => true,
                'unsigned' => true,
                'nullable' => false
            ],
            'Primary Key'
        )
            ->addColumn(
                'quote_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Quote Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                10,
                ['unsigned' => true, 'nullable' => true],
                'Store Id'
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => true, 'default' => null],
                'Customer ID'
            )
            ->addColumn(
                'email',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Email'
            )
            ->addColumn(
                'status',
                Table::TYPE_TEXT,
                10,
                ['nullable' => false, 'default' => 'pending'],
                'Status'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                [],
                'Created At'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                [],
                'Updated at'
            );
    }

    /**
     * @param $installer
     * @param $abandonedCartTable
     * @return mixed
     */
    private function addIndexKeyForAbandonedCarts($installer, $abandonedCartTable)
    {
        return $abandonedCartTable->addIndex(
            $installer->getIdxName('lx_email_abandoned_cart', ['quote_id']),
            ['quote_id']
        )
            ->addIndex(
                $installer->getIdxName('lx_email_abandoned_cart', ['store_id']),
                ['store_id']
            )
            ->addIndex(
                $installer->getIdxName('lx_email_abandoned_cart', ['customer_id']),
                ['customer_id']
            )
            ->addIndex(
                $installer->getIdxName('lx_email_abandoned_cart', ['email']),
                ['email']
            );
    }
}
