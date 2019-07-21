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
 * @category BinaryAnvil
 * @package BinaryAnvil_ExtendJobManager
 * @copyright Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\ExtendJobManager\Model;

use BinaryAnvil\JobManager\Model\Job as OriginalClass;
use Magento\Framework\Stdlib\DateTime\DateTime;

use BinaryAnvil\JobManager\Helper\Config;
use BinaryAnvil\JobManager\Model\ResourceModel\Job as JobResourceModel;

use Magento\Framework\Registry;
use Magento\Framework\Model\Context;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;

class Job extends OriginalClass
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     */
    protected $dateTime;

    /**
     * Job constructor.
     * @param DateTime $dateTime
     * @param TimezoneInterface $timezone
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        DateTime $dateTime,
        TimezoneInterface $timezone,
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->dateTime = $dateTime;
        parent::__construct($timezone, $context, $registry, $resource, $resourceCollection, $data);
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
            $this->setCreated($this->dateTime->gmtTimestamp());
        }

        return \Magento\Framework\Model\AbstractModel::beforeSave();
    }
}
