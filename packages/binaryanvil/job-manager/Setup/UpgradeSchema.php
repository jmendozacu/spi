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
 * @package     JobManager
 * @copyright   Copyright (c) 2016-present Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\JobManager\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use BinaryAnvil\JobManager\Helper\Config;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(Config::SCHEMA_JOB_TABLE_NAME),
                Config::SCHEMA_JOB_FIELD_SOURCE,
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'Job Source'
                ]
            );
        }
        //add schedule column
        if (version_compare($context->getVersion(), '1.0.7', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(Config::SCHEMA_JOB_TABLE_NAME),
                Config::SCHEMA_JOB_FIELD_SCHEDULE,
                [
                    'type' => Table::TYPE_TIMESTAMP,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Job Schedule Date'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.9', '<')) {
            $this->installJobHistoryTable($setup);
        }

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->installJobsArchiveTable($setup);
        }

        if (version_compare($context->getVersion(), '1.1.1', '<')) {
            $this->updateJobHistoryMessageField($setup);
        }

        $setup->endSetup();
    }

    /**
     * Install job history table
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return void
     * @throws \Zend_Db_Exception
     */
    protected function installJobHistoryTable(SchemaSetupInterface $setup)
    {
        $jobHistory = $setup->getConnection()
            ->newTable(Config::SCHEMA_JOB_HISTORY_TABLE_NAME)
            ->addColumn(
                Config::SCHEMA_JOB_HISTORY_FIELD_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ],
                'Job History Id'
            )->addColumn(
                Config::SCHEMA_JOB_FIELD_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                ],
                'Job Id'
            )->addColumn(
                Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Job Message'
            )->addColumn(
                Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE_TYPE,
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => Config::JOB_MESSAGE_TYPE_NOTICE],
                'Job Message Type'
            )
            ->addColumn(
                Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE_TIME,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Job Message Time'
            )->addForeignKey(
                $setup->getFkName(
                    Config::SCHEMA_JOB_HISTORY_TABLE_NAME,
                    Config::SCHEMA_JOB_FIELD_ID,
                    Config::SCHEMA_JOB_TABLE_NAME,
                    Config::SCHEMA_JOB_FIELD_ID
                ),
                Config::SCHEMA_JOB_FIELD_ID,
                $setup->getTable(Config::SCHEMA_JOB_TABLE_NAME),
                Config::SCHEMA_JOB_FIELD_ID,
                Table::ACTION_CASCADE
            );

        $setup->getConnection()->createTable($jobHistory);
    }

    /**
     * Install jobs archive table
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return void
     * @throws \Zend_Db_Exception
     */
    protected function installJobsArchiveTable($setup)
    {
        $jobsArchive = $setup->getConnection()
            ->newTable($setup->getTable(Config::SCHEMA_JOB_ARCHIVE_TABLE_NAME))
            ->addColumn(
                Config::SCHEMA_JOB_FIELD_ID,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Job Id'
            )->addColumn(
                Config::SCHEMA_JOB_FIELD_TYPE,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Job Type'
            )->addColumn(
                Config::SCHEMA_JOB_FIELD_DETAILS,
                Table::TYPE_TEXT,
                16777216,
                ['nullable' => false, 'default' => ''],
                'Job Details'
            )->addColumn(
                Config::SCHEMA_JOB_FIELD_STATUS,
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => Config::JOB_STATUS_PENDING],
                'Job Status'
            )->addColumn(
                Config::SCHEMA_JOB_FIELD_PRIORITY,
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => Config::JOB_PRIORITY_LOWEST],
                'Job Priority'
            )->addColumn(
                Config::SCHEMA_JOB_FIELD_CREATED,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Job Created At'
            )->addColumn(
                Config::SCHEMA_JOB_FIELD_EXECUTED,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true, 'default' => null],
                'Job Executed At'
            )->addColumn(
                Config::SCHEMA_JOB_FIELD_ATTEMPTS,
                Table::TYPE_SMALLINT,
                3,
                ['nullable' => false, 'default' => 0],
                'Job Attemts Number'
            )->addColumn(
                Config::SCHEMA_JOB_FIELD_LAST_ATTEMPT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true, 'default' => null],
                'Job Last Execution Attempt At'
            )->addColumn(
                Config::SCHEMA_JOB_FIELD_LAST_ERROR,
                Table::TYPE_TEXT,
                16777216,
                ['nullable' => true, 'default' => null],
                'Job Last Error Text'
            )->addColumn(
                Config::SCHEMA_JOB_FIELD_SOURCE,
                Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => ''],
                'Job Source'
            )->addColumn(
                Config::SCHEMA_JOB_FIELD_SCHEDULE,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true, 'default' => null],
                'Job Schedule Date'
            )->addIndex(
                $setup->getIdxName(Config::SCHEMA_JOB_TABLE_NAME, [Config::SCHEMA_JOB_FIELD_ID]),
                [Config::SCHEMA_JOB_FIELD_ID]
            )->addIndex(
                $setup->getIdxName(Config::SCHEMA_JOB_TABLE_NAME, [Config::SCHEMA_JOB_FIELD_TYPE]),
                [Config::SCHEMA_JOB_FIELD_TYPE]
            )->addIndex(
                $setup->getIdxName(Config::SCHEMA_JOB_TABLE_NAME, [Config::SCHEMA_JOB_FIELD_CREATED]),
                [Config::SCHEMA_JOB_FIELD_CREATED]
            )->addIndex(
                $setup->getIdxName(Config::SCHEMA_JOB_TABLE_NAME, [Config::SCHEMA_JOB_FIELD_LAST_ATTEMPT]),
                [Config::SCHEMA_JOB_FIELD_LAST_ATTEMPT]
            )->addIndex(
                $setup->getIdxName(Config::SCHEMA_JOB_TABLE_NAME, [Config::SCHEMA_JOB_FIELD_STATUS]),
                [Config::SCHEMA_JOB_FIELD_STATUS]
            );

        $setup->getConnection()->createTable($jobsArchive);
    }

    /**
     * Update job history message field to text
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return void
     * @throws \Zend_Db_Exception
     */
    protected function updateJobHistoryMessageField($setup)
    {
        $setup->getConnection()->changeColumn(
            $setup->getTable(Config::SCHEMA_JOB_HISTORY_TABLE_NAME),
            Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE,
            Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE,
            [
                'type' => Table::TYPE_TEXT,
                'length' => 16777216,
            ]
        );
    }
}
