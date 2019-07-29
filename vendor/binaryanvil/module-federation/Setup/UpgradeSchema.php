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
 * @package     BinaryAnvil_Federation
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\Federation\Setup;

use BinaryAnvil\Federation\Api\Data\SocialFeedItemInterface;
use BinaryAnvil\Federation\Model\ResourceModel\SocialFeedItem;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use BinaryAnvil\Federation\Api\Data\SocialFeedInterface;
use BinaryAnvil\Federation\Model\ResourceModel\SocialFeed;

/**
 * Class UpgradeSchema
 * @package BinaryAnvil\Federation\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->installSocialFeedInstanceTable($installer);
        }

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->installSocialFeedItemTable($installer);
        }
        
        $installer->endSetup();
    }

    /**
     * Create new table 'binaryanvil_social_instance'
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     * @return void
     */
    private function installSocialFeedInstanceTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(SocialFeed::DB_SCHEMA_TABLE_ENTITY_NAME))
            ->addColumn(
                SocialFeedInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Social Feed Instance ID'
            )
            ->addColumn(
                SocialFeedInterface::KEY,
                Table::TYPE_TEXT,
                255,
                [],
                'Social Feed Instance Key'
            )
            ->addColumn(
                SocialFeedInterface::BLOCK_CLASS,
                Table::TYPE_TEXT,
                255,
                [],
                'Social Feed Block Class Name'
            )
            ->addColumn(
                SocialFeedInterface::PACKAGE,
                Table::TYPE_TEXT,
                255,
                [],
                'Social Feed Package (Module)'
            )
            ->setComment('Binary Anvil Social Feeds Instance');

        $installer->getConnection()->createTable($table);
    }

    /**
     * Create new table 'binaryanvil_social_instance_item'
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     * @return void
     */
    private function installSocialFeedItemTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(SocialFeedItem::DB_SCHEMA_TABLE_ENTITY_NAME))
            ->addColumn(
                SocialFeedItemInterface::ITEM_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Social Feed Item ID'
            )->addColumn(
                SocialFeedItemInterface::INSTANCE_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Social Feed Instance ID'
            )->addColumn(
                SocialFeedItemInterface::IDENTIFIER,
                Table::TYPE_TEXT,
                255,
                [],
                'Social Feed Item Identifier'
            )->addColumn(
                SocialFeedItemInterface::CREATED_TIME,
                Table::TYPE_BIGINT,
                null,
                ['unsigned' => true],
                'Social Feed Item Created Time'
            )->addColumn(
                SocialFeedItemInterface::AUTHOR_LINK,
                Table::TYPE_TEXT,
                255,
                [],
                'Social Feed Item Author Link'
            )->addColumn(
                SocialFeedItemInterface::AUTHOR_PICTURE,
                Table::TYPE_TEXT,
                255,
                [],
                'Social Feed Item Author Picture'
            )->addColumn(
                SocialFeedItemInterface::AUTHOR_NAME,
                Table::TYPE_TEXT,
                255,
                [],
                'Social Feed Item Author Name'
            )->addColumn(
                SocialFeedItemInterface::MESSAGE,
                Table::TYPE_TEXT,
                null,
                [],
                'Social Feed Item Message'
            )->addColumn(
                SocialFeedItemInterface::DESCRIPTION,
                Table::TYPE_TEXT,
                null,
                [],
                'Social Feed Item Description'
            )->addColumn(
                SocialFeedItemInterface::LINK,
                Table::TYPE_TEXT,
                255,
                [],
                'Social Feed Item Link'
            )->addColumn(
                SocialFeedItemInterface::ATTACHMENT,
                Table::TYPE_TEXT,
                null,
                [],
                'Social Feed Item Attachment'
            )->addIndex(
                $installer->getIdxName(
                    $installer->getTable(SocialFeedItem::DB_SCHEMA_TABLE_ENTITY_NAME),
                    [
                        SocialFeedItemInterface::IDENTIFIER,
                        SocialFeedItemInterface::AUTHOR_NAME
                    ],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                [SocialFeedItemInterface::IDENTIFIER, SocialFeedItemInterface::AUTHOR_NAME],
                ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
            )->addForeignKey(
                $installer->getFkName(
                    SocialFeedItem::DB_SCHEMA_TABLE_ENTITY_NAME,
                    SocialFeedItemInterface::INSTANCE_ID,
                    SocialFeed::DB_SCHEMA_TABLE_ENTITY_NAME,
                    SocialFeedInterface::ENTITY_ID
                ),
                SocialFeedItemInterface::INSTANCE_ID,
                $installer->getTable(SocialFeed::DB_SCHEMA_TABLE_ENTITY_NAME),
                SocialFeedInterface::ENTITY_ID,
                Table::ACTION_CASCADE
            )->setComment('Binary Anvil Social Feeds Items');

        $installer->getConnection()->createTable($table);
    }
}
