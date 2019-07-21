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

namespace BinaryAnvil\Federation\Model;

use BinaryAnvil\Federation\Api\Data\SocialFeedItemInterface;
use Magento\Framework\Model\AbstractModel;
use BinaryAnvil\Federation\Model\ResourceModel\SocialFeedItem as SocialFeedItemResource;

/**
 * Class SocialFeed
 * @package BinaryAnvil\Federation\Model
 *
 * Business model for Social Feeds
 *
 * @codingStandardsIgnoreFile
 */
class SocialFeedItem extends AbstractModel implements SocialFeedItemInterface
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(SocialFeedItemResource::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ITEM_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($Id)
    {
        return $this->setData(self::ITEM_ID, $Id);
    }

    /**
     * {@inheritdoc}
     */
    public function getInstanceId()
    {
        return $this->getData(self::INSTANCE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setInstanceId($instanceId)
    {
        return $this->setData(self::INSTANCE_ID, $instanceId);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->getData(self::IDENTIFIER);
    }

    /**
     * {@inheritdoc}
     */
    public function setIdentifier($identifier)
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedTime()
    {
        return $this->getData(self::CREATED_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedTime($createdTime)
    {
        return $this->getData(self::CREATED_TIME, $createdTime);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorLink()
    {
        return $this->getData(self::AUTHOR_LINK);
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthorLink($authorLink)
    {
        return $this->getData(self::AUTHOR_LINK, $authorLink);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorPicture()
    {
        return $this->getData(self::AUTHOR_PICTURE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthorPicture($authorPicture)
    {
        return $this->getData(self::AUTHOR_PICTURE, $authorPicture);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorName()
    {
        return $this->getData(self::AUTHOR_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthorName($authorName)
    {
        return $this->getData(self::AUTHOR_NAME, $authorName);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        return $this->getData(self::MESSAGE, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        return $this->getData(self::DESCRIPTION, $description);
    }

    /**
     * {@inheritdoc}
     */
    public function getLink()
    {
        return $this->getData(self::LINK);
    }

    /**
     * {@inheritdoc}
     */
    public function setLink($link)
    {
        return $this->getData(self::LINK, $link);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttachment()
    {
        return $this->getData(self::ATTACHMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttachment($attachment)
    {
        return $this->getData(self::ATTACHMENT, $attachment);
    }
}
