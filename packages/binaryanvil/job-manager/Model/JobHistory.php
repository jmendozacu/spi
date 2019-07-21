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

namespace BinaryAnvil\JobManager\Model;

use BinaryAnvil\JobManager\Helper\Config;
use Magento\Framework\Model\AbstractModel;
use BinaryAnvil\JobManager\Api\Data\JobHistoryInterface;
use BinaryAnvil\JobManager\Model\ResourceModel\JobHistory as JobHistoryResourceModel;

class JobHistory extends AbstractModel implements JobHistoryInterface
{
    // @codingStandardsIgnoreStart
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(JobHistoryResourceModel::class);
    }
    // @codingStandardsIgnoreEnd

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_getData(Config::SCHEMA_JOB_HISTORY_FIELD_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getJobId()
    {
        return $this->_getData(Config::SCHEMA_JOB_FIELD_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->_getData(Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        return $this->setData(Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageType()
    {
        return (int)$this->_getData(Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMessageType($messageType)
    {
        return $this->setData(Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE_TYPE, $messageType);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageTime()
    {
        return $this->_getData(Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setMessageTime($messageTime)
    {
        return $this->setData(Config::SCHEMA_JOB_HISTORY_FIELD_MESSAGE_TIME, $messageTime);
    }
}
