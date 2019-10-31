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

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use BinaryAnvil\Federation\Model\ResourceModel\SocialFeed;

/**
 * Class Uninstall
 * @package BinaryAnvil\Federation\Setup
 */
class Uninstall implements UninstallInterface
{
    /**
     * {@inheritdoc}
     */
    public function uninstall(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        
        $installer->startSetup();

        $this->dropSocialFeedInstanceTable($installer);

        $installer->endSetup();
    }

    /**
     * Drop 'binaryanvil_social_instance' table
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     * @return void
     */
    private function dropSocialFeedInstanceTable(SchemaSetupInterface $installer)
    {
        $installer
            ->getConnection()
            ->dropTable($installer->getTable(SocialFeed::DB_SCHEMA_TABLE_ENTITY_NAME));
    }
}
