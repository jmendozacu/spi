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

use Magento\Framework\Registry;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Data\Collection\AbstractDb;
use BinaryAnvil\JobManager\Api\JobRunResultInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;

class JobRunResult extends AbstractModel implements JobRunResultInterface
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    protected $serializer;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Json $serializer,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $data = [
            self::FIELD_NAME_ERRORS => '',
            self::FIELD_NAME_STATUS => false,
        ];

        $this->serializer = $serializer;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        return json_decode($this->_getData(self::FIELD_NAME_ERRORS));
    }

    /**
     * {@inheritdoc}
     */
    public function setErrors(array $errors = [])
    {
        $this->setData(self::FIELD_NAME_ERRORS, json_encode($errors));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSuccess()
    {
        return $this->_getData(self::FIELD_NAME_SUCCESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setSuccess($success)
    {
        $this->setData(self::FIELD_NAME_SUCCESS, $success);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNotice()
    {
        return $this->_getData(self::FIELD_NAME_NOTICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setNotice(array $notice = [])
    {
        $this->setData(self::FIELD_NAME_NOTICE, $this->serializer->serialize($notice));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        return $this->_getData(self::FIELD_NAME_REQUEST);
    }

    /**
     * {@inheritdoc}
     */
    public function setRequest(array $request = [])
    {
        $this->setData(self::FIELD_NAME_REQUEST, $this->serializer->serialize($request));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        return $this->_getData(self::FIELD_NAME_RESPONSE);
    }

    /**
     * {@inheritdoc}
     */
    public function setResponse(array $response = [])
    {
        $this->setData(self::FIELD_NAME_RESPONSE, $this->serializer->serialize($response));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isDone()
    {
        return (bool) $this->_getData(self::FIELD_NAME_STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setDone($status = false)
    {
        $this->setData(self::FIELD_NAME_STATUS, $status);

        return $this;
    }
}
