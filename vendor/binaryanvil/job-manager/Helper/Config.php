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

namespace BinaryAnvil\JobManager\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    /**
     * Schema settings
     */
    const SCHEMA_JOB_FIELD_ID = 'job_id';

    const SCHEMA_JOB_FIELD_TYPE = 'type';

    const SCHEMA_JOB_FIELD_STATUS = 'status';

    const SCHEMA_JOB_FIELD_PRIORITY = 'priority';

    const SCHEMA_JOB_FIELD_ATTEMPTS = 'attempts';

    const SCHEMA_JOB_FIELD_LAST_ATTEMPT = 'last_attempt';

    const SCHEMA_JOB_FIELD_LAST_ERROR = 'last_error';

    const SCHEMA_JOB_FIELD_EXECUTED = 'executed';

    const SCHEMA_JOB_FIELD_SCHEDULE = 'schedule';

    const SCHEMA_JOB_FIELD_CREATED = 'created';

    const SCHEMA_JOB_FIELD_DETAILS = 'details';

    const SCHEMA_JOB_FIELD_SOURCE = 'source';

    const SCHEMA_JOB_TABLE_NAME = 'binaryanvil_jobmanager';

    const SCHEMA_JOB_HISTORY_TABLE_NAME = 'binaryanvil_jobmanager_hystory';

    const SCHEMA_JOB_ARCHIVE_TABLE_NAME = 'binaryanvil_jobmanager_archive';

    const SCHEMA_JOB_HISTORY_FIELD_ID = 'history_id';

    const SCHEMA_JOB_HISTORY_FIELD_MESSAGE = 'message';

    const SCHEMA_JOB_HISTORY_FIELD_MESSAGE_TYPE = 'message_type';

    const SCHEMA_JOB_HISTORY_FIELD_MESSAGE_TIME = 'message_time';

    /**
     * Purge strategy values
     */
    const JOB_PURGE_STRATEGY_DELETE         = 1;
    const JOB_PURGE_STRATEGY_DELETE_LABEL   = 'Delete';

    const JOB_PURGE_STRATEGY_ARCHIVE         = 2;
    const JOB_PURGE_STRATEGY_ARCHIVE_LABEL   = 'Archive';

    /**
     * Status values
     */
    const JOB_STATUS_PENDING         = 1;
    const JOB_STATUS_PENDING_LABEL   = 'Pending';

    const JOB_STATUS_EXECUTED        = 2;
    const JOB_STATUS_EXECUTED_LABEL  = 'Executed';

    const JOB_STATUS_ERROR           = 3;
    const JOB_STATUS_ERROR_LABEL     = 'Error';

    const JOB_STATUS_RUNNING           = 4;
    const JOB_STATUS_RUNNING_LABEL     = 'Running';

    /**
     * Priority values
     */
    const JOB_PRIORITY_HIGHEST       = 1;
    const JOB_PRIORITY_HIGHEST_LABEL = 'Highest';

    const JOB_PRIORITY_HIGH          = 2;
    const JOB_PRIORITY_HIGH_LABEL    = 'High';

    const JOB_PRIORITY_MEDIUM        = 3;
    const JOB_PRIORITY_MEDIUM_LABEL  = 'Medium';

    const JOB_PRIORITY_LOW           = 4;
    const JOB_PRIORITY_LOW_LABEL     = 'Low';

    const JOB_PRIORITY_LOWEST        = 5;
    const JOB_PRIORITY_LOWEST_LABEL  = 'Lowest';

    /**
     * Messages type values
     */
    const JOB_MESSAGE_TYPE_ERROR            = 1;
    const JOB_MESSAGE_TYPE_ERROR_LABEL      = 'Error';

    const JOB_MESSAGE_TYPE_SUCCESS          = 2;
    const JOB_MESSAGE_TYPE_SUCCESS_LABEL    = 'Success';

    const JOB_MESSAGE_TYPE_NOTICE           = 3;
    const JOB_MESSAGE_TYPE_NOTICE_LABEL     = 'Notice';

    const JOB_MESSAGE_TYPE_REQUEST          = 4;
    const JOB_MESSAGE_TYPE_REQUEST_LABEL    = 'Request';

    const JOB_MESSAGE_TYPE_RESPONSE         = 5;
    const JOB_MESSAGE_TYPE_RESPONSE_LABEL   = 'Response';

    /**
     * Defaults settings
     */
    const DEFAULT_PAGE_SIZE = 10;

    const DEFAULT_PURGE_EXECUTED = 15;

    const DEFAULT_PURGE_ERROR = 30;

    const DEFAULT_PURGE_ARCHIVE = 90;

    const DEFAULT_DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * Messaging
     */
    const MESSAGE_JOB_FAILED_GENERIC = 'Something went wrong while saving the job data. Please review the error log.';

    const MESSAGE_JOB_SAVE_GENERIC = 'Job saved successfully.';

    const MESSAGE_JOB_FAILED = 'Job type[%type],  id[%id] - failed to execute';

    const MESSAGE_JOB_SUCCESS = 'Job type[%type],  id[%id] - executed successfully';

    const MESSAGE_JOB_DELETE_FAILED = 'Failed to delete job. Please refer to server logs for more information.';

    const MESSAGE_JOB_DELETE_SUCCESS = 'Job type[%type],  id[%id] - successfully deleted';

    const MESSAGE_JOB_DELETE_COUNT = 'Successfully deleted %count job(s).';

    const MESSAGE_JOB_UPDATE_FAILED = 'Failed to update job(s). Please refer to server logs for more information.';

    const MESSAGE_JOB_UPDATE_SUCCESS = 'Job type[%type],  id[%id] - successfully changed %param to "%value"';

    const MESSAGE_JOB_UPDATE_COUNT = 'Successfully changed %param to "%value" for %count job(s)';

    /**
     * Config
     */
    const JOB_LOCK_FILE = 'lock/jobmanager.lock';

    /**
     * XML paths
     */
    const XML_PATH_IS_ENABLED = 'binaryanvil_jobmanager/general/is_enabled';

    const XML_PATH_DEBUG_MODE = 'binaryanvil_jobmanager/general/debug_mode';

    const XML_PATH_ATTEMPTS = 'binaryanvil_jobmanager/jobs/attempts';

    const XML_PATH_LIMIT = 'binaryanvil_jobmanager/jobs/limit';

    const XML_PATH_PURGE_EXECUTED = 'binaryanvil_jobmanager/purge/executed';

    const XML_PATH_PURGE_ERROR = 'binaryanvil_jobmanager/purge/error';

    const XML_PATH_PURGE_STRATEGY = 'binaryanvil_jobmanager/purge/strategy';

    const XML_PATH_PURGE_ARCHIVE_KEEP = 'binaryanvil_jobmanager/purge/archive_keep';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);

        $this->scopeConfig = $context->getScopeConfig();
        $this->storeManager = $storeManager;
    }

    /**
     * Get configuration setting
     *
     * @param string $path
     * @param string $scopeType
     * @param int|null $store
     * @return string|bool
     */
    public function getConfig($path, $scopeType = ScopeInterface::SCOPE_STORE, $store = null)
    {
        if ($store === null) {
            try {
                $store = $this->storeManager->getStore()->getId();
            } catch (\Exception $e) {
                return false;
            }
        }

        return $this->scopeConfig->getValue(
            $path,
            $scopeType,
            $store
        );
    }

    /**
     * Retrieve priorities as an array
     *
     * @return array
     */
    public function priorityToArray()
    {
        return [
            static::JOB_PRIORITY_HIGHEST => static::JOB_PRIORITY_HIGHEST_LABEL,
            static::JOB_PRIORITY_HIGH => static::JOB_PRIORITY_HIGH_LABEL,
            static::JOB_PRIORITY_MEDIUM => static::JOB_PRIORITY_MEDIUM_LABEL,
            static::JOB_PRIORITY_LOW => static::JOB_PRIORITY_LOW_LABEL,
            static::JOB_PRIORITY_LOWEST => static::JOB_PRIORITY_LOWEST_LABEL,
        ];
    }

    /**
     * Retrieve statuses as an array
     *
     * @return array
     */
    public function statusToArray()
    {
        return [
            static::JOB_STATUS_PENDING => static::JOB_STATUS_PENDING_LABEL,
            static::JOB_STATUS_EXECUTED => static::JOB_STATUS_EXECUTED_LABEL,
            static::JOB_STATUS_ERROR => static::JOB_STATUS_ERROR_LABEL,
            static::JOB_STATUS_RUNNING => static::JOB_STATUS_RUNNING_LABEL,
        ];
    }

    /**
     * Retrieve messages type as an array
     *
     * @return array
     */
    public function messagesTypeToArray()
    {
        return [
            static::JOB_MESSAGE_TYPE_ERROR => static::JOB_MESSAGE_TYPE_ERROR_LABEL,
            static::JOB_MESSAGE_TYPE_SUCCESS => static::JOB_MESSAGE_TYPE_SUCCESS_LABEL,
            static::JOB_MESSAGE_TYPE_NOTICE => static::JOB_MESSAGE_TYPE_NOTICE_LABEL,
            static::JOB_MESSAGE_TYPE_REQUEST => static::JOB_MESSAGE_TYPE_REQUEST_LABEL,
            static::JOB_MESSAGE_TYPE_RESPONSE => static::JOB_MESSAGE_TYPE_RESPONSE_LABEL,
        ];
    }

    /**
     * Retrieve purge strategy as an array
     *
     * @return array
     */
    public function purgeStrategyToArray()
    {
        return [
            static::JOB_PURGE_STRATEGY_DELETE => static::JOB_PURGE_STRATEGY_DELETE_LABEL,
            static::JOB_PURGE_STRATEGY_ARCHIVE => static::JOB_PURGE_STRATEGY_ARCHIVE_LABEL,
        ];
    }

    /**
     * Check if module is in debug mode
     *
     * @return bool
     */
    public function isDebugMode()
    {
        return (bool) $this->getConfig(self::XML_PATH_DEBUG_MODE);
    }
}
