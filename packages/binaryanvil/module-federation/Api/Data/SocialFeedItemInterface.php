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

namespace BinaryAnvil\Federation\Api\Data;

/**
 * Interface SocialFeedItemInterface
 * @package BinaryAnvil\Federation\Api\Data
 */
interface SocialFeedItemInterface
{
    /**#@+
     *
     * Field list for
     * 'binaryanvil_social_instance_item' table
     *
     * @type string
     */
    const ITEM_ID           = 'item_id';
    const INSTANCE_ID       = 'instance_id';
    const IDENTIFIER        = 'identifier';
    const CREATED_TIME      = 'created_time';
    const AUTHOR_LINK       = 'author_link';
    const AUTHOR_PICTURE    = 'author_picture';
    const AUTHOR_NAME       = 'author_name';
    const MESSAGE           = 'message';
    const DESCRIPTION       = 'description';
    const LINK              = 'link';
    const ATTACHMENT        = 'attachment';
    /**#@- */

    /**
     * Get item_id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set item_id
     *
     * @param int $Id
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedItemInterface
     */
    public function setId($Id);

    /**
     * Get instance_id
     *
     * @return int|null
     */
    public function getInstanceId();

    /**
     * Set instance_id
     *
     * @param int $instanceId
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedItemInterface
     */
    public function setInstanceId($instanceId);

    /**
     * Get identifier
     *
     * @return string|null
     */
    public function getIdentifier();

    /**
     * Set identifier
     *
     * @param string $identifier
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedItemInterface
     */
    public function setIdentifier($identifier);

    /**
     * Get created_time
     *
     * @return string|null
     */
    public function getCreatedTime();

    /**
     * Set created_time
     *
     * @param int $createdTime
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedItemInterface
     */
    public function setCreatedTime($createdTime);

    /**
     * Get author_link
     *
     * @return int|null
     */
    public function getAuthorLink();

    /**
     * Set author_link
     *
     * @param string $authorLink
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedItemInterface
     */
    public function setAuthorLink($authorLink);

    /**
     * Get author_picture
     *
     * @return string|null
     */
    public function getAuthorPicture();

    /**
     * Set author_picture
     *
     * @param string $authorPicture
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedItemInterface
     */
    public function setAuthorPicture($authorPicture);

    /**
     * Get author_name
     *
     * @return string|null
     */
    public function getAuthorName();

    /**
     * Set author_name
     *
     * @param string $authorName
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedItemInterface
     */
    public function setAuthorName($authorName);

    /**
     * Get message
     *
     * @return string|null
     */
    public function getMessage();

    /**
     * Set message
     *
     * @param string $message
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedItemInterface
     */
    public function setMessage($message);

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     *
     * @param string $description
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedItemInterface
     */
    public function setDescription($description);

    /**
     * Get link
     *
     * @return string|null
     */
    public function getLink();

    /**
     * Set link
     *
     * @param string $link
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedItemInterface
     */
    public function setLink($link);

    /**
     * Get attachment
     *
     * @return string|null
     */
    public function getAttachment();

    /**
     * Set attachment
     *
     * @param string $attachment
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedItemInterface
     */
    public function setAttachment($attachment);
}
