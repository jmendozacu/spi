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
use BinaryAnvil\JobManager\Api\Data\JobInterface;
use BinaryAnvil\JobManager\Model\ResourceModel\Job as JobResourceModel;

use Magento\Framework\Registry;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;

class Job extends AbstractModel implements JobInterface
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * Job business model constructor
     *
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        TimezoneInterface $timezone,
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->timezone = $timezone;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    // @codingStandardsIgnoreStart
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(JobResourceModel::class);
    }
    // @codingStandardsIgnoreEnd

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_getData(Config::SCHEMA_JOB_FIELD_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->_getData(Config::SCHEMA_JOB_FIELD_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        return $this->setData(Config::SCHEMA_JOB_FIELD_TYPE, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->_getData(Config::SCHEMA_JOB_FIELD_PRIORITY);
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority($priority)
    {
        return $this->setData(Config::SCHEMA_JOB_FIELD_PRIORITY, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->_getData(Config::SCHEMA_JOB_FIELD_STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(Config::SCHEMA_JOB_FIELD_STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails()
    {
        return json_decode($this->_getData(Config::SCHEMA_JOB_FIELD_DETAILS), true);
    }

    /**
     * {@inheritdoc}
     */
    public function setDetails($details = '')
    {
        return $this->setData(Config::SCHEMA_JOB_FIELD_DETAILS, json_encode($details));
    }

    /**
     * {@inheritdoc}
     */
    public function getAttempt()
    {
        return (int) $this->_getData(Config::SCHEMA_JOB_FIELD_ATTEMPTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttempt($attempt = 0)
    {
        return $this->setData(Config::SCHEMA_JOB_FIELD_ATTEMPTS, $attempt);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreated()
    {
        return $this->_getData(Config::SCHEMA_JOB_FIELD_CREATED);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreated($datetime = '')
    {
        return $this->setData(Config::SCHEMA_JOB_FIELD_CREATED, $datetime);
    }

    /**
     * {@inheritdoc}
     */
    public function getExecuted()
    {
        return $this->_getData(Config::SCHEMA_JOB_FIELD_EXECUTED);
    }

    /**
     * {@inheritdoc}
     */
    public function setExecuted($datetime = '')
    {
        return $this->setData(Config::SCHEMA_JOB_FIELD_EXECUTED, $datetime);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastAttempt()
    {
        return $this->_getData(Config::SCHEMA_JOB_FIELD_LAST_ATTEMPT);
    }

    /**
     * {@inheritdoc}
     */
    public function setLastAttempt($datetime = '')
    {
        return $this->setData(Config::SCHEMA_JOB_FIELD_LAST_ATTEMPT, $datetime);
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return $this->_getData(Config::SCHEMA_JOB_FIELD_SOURCE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSource($source = '')
    {
        return $this->setData(Config::SCHEMA_JOB_FIELD_SOURCE, $source);
    }

    /**
     * {@inheritdoc}
     */
    public function getSchedule()
    {
        return $this->_getData(Config::SCHEMA_JOB_FIELD_SCHEDULE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSchedule($datetime)
    {
        if (!is_int($datetime)) {
            $datetime = strtotime($datetime);
        }
        $datetime = date(Config::DEFAULT_DATE_TIME_FORMAT, $datetime);
        return $this->setData(Config::SCHEMA_JOB_FIELD_SCHEDULE, $datetime);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastError()
    {
        $errors = $this->_getData(Config::SCHEMA_JOB_FIELD_LAST_ERROR);
        $isJson = json_decode($errors);

        return (json_last_error() == JSON_ERROR_NONE) ? $isJson : $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastError($errors = '')
    {
        if (is_array($errors)) {
            $errors = json_encode($errors);
        }
        return $this->setData(Config::SCHEMA_JOB_FIELD_LAST_ERROR, $errors);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        if (empty($this->getType())) {
            throw new CouldNotSaveException(__('JOB MANAGER: Job type cannot be empty.'));
        }

        if (empty($this->getDetails())) {
            $this->setDetails([]);
        }

        if (empty($this->getPriority())) {
            $this->setPriority(Config::JOB_PRIORITY_LOWEST);
        }

        if (empty($this->getCreated())) {
            $this->setCreated($this->timezone->scopeTimeStamp());
        }

        return parent::beforeSave();
    }
}
