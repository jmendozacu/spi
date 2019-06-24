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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use BinaryAnvil\Ratings\Preference\Magento\Review\Model\Rating;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    const RATING_TABLE_NAME = 'rating';

    /**
     * Installs DB schema for a module
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $context->getVersion();

        $installer->startSetup();
        $ratingTable = $installer->getTable(self::RATING_TABLE_NAME);
        $columns = [
            Rating::IS_USED_IN_SUMMARY => [
                'type' => Table::TYPE_BOOLEAN,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Use in summary rating'
            ],
            Rating::LABEL_MIN => [
                'type' => Table::TYPE_TEXT,
                'length' => 256,
                'nullable' => true,
                'comment' => 'Label for min value'
            ],
            Rating::LABEL_PERFECT => [
                'type' => Table::TYPE_TEXT,
                'length' => 256,
                'nullable' => true,
                'comment' => 'Label for perfect value'
            ],
            Rating::LABEL_MAX => [
                'type' => Table::TYPE_TEXT,
                'length' => 256,
                'nullable' => true,
                'comment' => 'Label for max value'
            ]
        ];

        $connection = $installer->getConnection();
        foreach ($columns as $name => $definition) {
            $connection->addColumn($ratingTable, $name, $definition);
        }

        $installer->endSetup();
    }
}
