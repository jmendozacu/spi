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
 * @package     BinaryAnvil_Ratings
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\Ratings\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use BinaryAnvil\Ratings\Preference\Magento\Review\Model\Review;
use Magento\Framework\DB\Ddl\Table;
use BinaryAnvil\Ratings\Model\ReviewHelpful as ReviewHelpfulModel;
use BinaryAnvil\Ratings\Model\ResourceModel\ReviewHelpful as ReviewHelpfulResourceModel;
use Zend_Db_Exception;

class UpgradeSchema implements UpgradeSchemaInterface
{
    const REVIEW_DETAIL_TABLE_NAME = 'review_detail';

    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addIsRecommendProductField($setup);
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->createReviewHelpfulTable($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     * @return void
     */
    private function addIsRecommendProductField(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $connection->addColumn(
            $installer->getTable(self::REVIEW_DETAIL_TABLE_NAME),
            Review::IS_RECOMMEND_PRODUCT,
            [
                'type' => Table::TYPE_BOOLEAN,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Customer recommend this product'
            ]
        );
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     * @throws Zend_Db_Exception
     */
    private function createReviewHelpfulTable(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $table = $connection->newTable(ReviewHelpfulResourceModel::DB_SCHEMA_REVIEW_HELPFUL_TABLE)
            ->addColumn(
                ReviewHelpfulModel::HELPFUL_ID,
                Table::TYPE_BIGINT,
                20,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'ID'
            )->addColumn(
                ReviewHelpfulModel::REVIEW_ID,
                Table::TYPE_BIGINT,
                20,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Review ID'
            )->addColumn(
                ReviewHelpfulModel::CUSTOMER_ID,
                Table::TYPE_INTEGER,
                10,
                [
                    'unsigned' => true,
                    'nullable' => true
                ],
                'Customer ID'
            )->addColumn(
                ReviewHelpfulModel::IS_HELPFUL,
                Table::TYPE_BOOLEAN,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Review is Helpful'
            )->addForeignKey(
                $installer->getFkName(
                    ReviewHelpfulResourceModel::DB_SCHEMA_REVIEW_HELPFUL_TABLE,
                    ReviewHelpfulModel::REVIEW_ID,
                    'review',
                    'review_id'
                ),
                ReviewHelpfulModel::REVIEW_ID,
                $installer->getTable('review'),
                'review_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    ReviewHelpfulResourceModel::DB_SCHEMA_REVIEW_HELPFUL_TABLE,
                    ReviewHelpfulModel::CUSTOMER_ID,
                    'customer_entity',
                    'entity_id'
                ),
                ReviewHelpfulModel::CUSTOMER_ID,
                $installer->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            );

        $connection->createTable($table);
    }
}
