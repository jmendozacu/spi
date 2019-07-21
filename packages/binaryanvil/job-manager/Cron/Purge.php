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

namespace BinaryAnvil\JobManager\Cron;

use Magento\Framework\App\ResourceConnection;
use BinaryAnvil\JobManager\Model\Adapter;
use BinaryAnvil\JobManager\Helper\Config;
use BinaryAnvil\JobManager\Helper\Data;

class Purge
{
    const QUERY_LIMIT = 5000;

    const DEL_QUERY = 'delete from `%s` 
        where (`%s` = %s and `%s` < \'%s\') OR (`%s` = %s and `%s` < \'%s\') LIMIT %s';

    const ARCH_QUERY = 'insert into %s select * from %s 
        where (`%s` = %s and `%s` < \'%s\') OR (`%s` = %s and `%s` < \'%s\') LIMIT %s';

    const ARCH_DEL_QUERY = 'delete from `%s` where `%s` < \'%s\' LIMIT %s';

    /**
     * @var string $jobPurgeStart
     */
    private $jobPurgeStart = 'Purging jobs by schedule...';

    /**
     * @var string $jobArchivePurgeStart
     */
    private $jobArchivePurgeStart = 'Purging archived jobs by schedule...';

    /**
     * @var string $jobPurgeSuccess
     */
    private $jobPurgeSuccess = 'Successfully purged %count job(s)';

    /**
     * @var string $jobPurgeArchived
     */
    private $jobPurgeArchived  = 'Successfully archived %count job(s)';

    /**
     * @var array $jobPurgeStrategy
     */
    private $jobPurgeStrategy = [
        'delete' => ' with deleting strategy.',
        'archive' => ' with archiving strategy.',
    ];

    /**
     * @var \BinaryAnvil\JobManager\Model\Adapter $adapter
     */
    private $adapter;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface $connection
     */
    protected $connection;

    /**
     * @var \Magento\Framework\App\ResourceConnection $resource
     */
    protected $resource;

    /**
     * @var \BinaryAnvil\JobManager\Helper\Data $helper
     */
    protected $helper;

    /**
     * @param \BinaryAnvil\JobManager\Helper\Data $helper
     * @param \BinaryAnvil\JobManager\Model\Adapter $adapter
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        Data $helper,
        Adapter $adapter,
        ResourceConnection $resource
    ) {
        $this->helper = $helper;
        $this->adapter = $adapter;
        $this->resource = $resource;
    }

    /**
     * Execute scheduled jobs
     *
     * Safety break is set up for 50 seconds
     * to reflect crontab.xml settings.
     *
     * @return bool
     */
    public function execute()
    {
        if (!$this->helper->isEnabled()) {
            return false;
        }

        if ($this->helper->isStrategyArchive()) {
            $this->archive();
            $this->archivePurge();
        }

        $this->delete();

        return false;
    }

    /**
     * Retrieve resource connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        }
        return $this->connection;
    }

    /**
     * Delete jobs
     */
    protected function delete()
    {
        if (!$this->helper->isStrategyArchive()) {
            $this->helper->log->info(__($this->jobPurgeStart) . __($this->jobPurgeStrategy['delete']));
        }

        try {
            $table = $this->resource->getTableName(Config::SCHEMA_JOB_TABLE_NAME);

            $deleteQuery = sprintf(
                static::DEL_QUERY,
                $table,
                Config::SCHEMA_JOB_FIELD_STATUS,
                Config::JOB_STATUS_EXECUTED,
                Config::SCHEMA_JOB_FIELD_CREATED,
                $this->helper->getPurgeExecuted(),
                Config::SCHEMA_JOB_FIELD_STATUS,
                Config::JOB_STATUS_ERROR,
                Config::SCHEMA_JOB_FIELD_CREATED,
                $this->helper->getPurgeError(),
                static::QUERY_LIMIT
            );

            $result = $this->getConnection()->query($deleteQuery);

            if ($result->rowCount() && !$this->helper->isStrategyArchive()) {
                $this->helper->log->info(__($this->jobPurgeSuccess, ['count' => $result->rowCount()]));
            }

            return true;
        } catch (\Exception $e) {
            $this->helper->log->critical($e);
        }
    }

    /**
     * Archive jobs
     */
    protected function archive()
    {
        $this->helper->log->info(__($this->jobPurgeStart) . __($this->jobPurgeStrategy['archive']));

        try {
            $table = $this->resource->getTableName(Config::SCHEMA_JOB_TABLE_NAME);
            $archive = $this->resource->getTableName(Config::SCHEMA_JOB_ARCHIVE_TABLE_NAME);

            $archiveQuery = sprintf(
                static::ARCH_QUERY,
                $archive,
                $table,
                Config::SCHEMA_JOB_FIELD_STATUS,
                Config::JOB_STATUS_EXECUTED,
                Config::SCHEMA_JOB_FIELD_CREATED,
                $this->helper->getPurgeExecuted(),
                Config::SCHEMA_JOB_FIELD_STATUS,
                Config::JOB_STATUS_ERROR,
                Config::SCHEMA_JOB_FIELD_CREATED,
                $this->helper->getPurgeError(),
                static::QUERY_LIMIT
            );

            $result = $this->getConnection()->query($archiveQuery);

            if ($result->rowCount()) {
                $this->helper->log->info(__($this->jobPurgeArchived, ['count' => $result->rowCount()]));
            }

            return true;
        } catch (\Exception $e) {
            $this->helper->log->critical($e);
        }
    }

    /**
     * Purge jobs from archive
     */
    protected function archivePurge()
    {
        $this->helper->log->info(__($this->jobArchivePurgeStart));

        $table = $this->resource->getTableName(Config::SCHEMA_JOB_ARCHIVE_TABLE_NAME);

        try {
            $deleteQuery = sprintf(
                static::ARCH_DEL_QUERY,
                $table,
                Config::SCHEMA_JOB_FIELD_CREATED,
                $this->helper->getArchivePurge(),
                static::QUERY_LIMIT
            );

            $result = $this->getConnection()->query($deleteQuery);

            if ($result->rowCount()) {
                $this->helper->log->info(__($this->jobPurgeSuccess, ['count' => $result->rowCount()]));
            }
        } catch (\Exception $e) {
            $this->helper->log->critical($e);
        }
    }
}
