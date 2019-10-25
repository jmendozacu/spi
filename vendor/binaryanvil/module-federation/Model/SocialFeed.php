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

use Magento\Framework\Model\AbstractModel;
use BinaryAnvil\Federation\Api\Data\SocialFeedInterface;
use BinaryAnvil\Federation\Model\ResourceModel\SocialFeed as SocialFeedResource;

/**
 * Class SocialFeed
 * @package BinaryAnvil\Federation\Model
 *
 * Business model for Social Feeds
 *
 * @codingStandardsIgnoreFile
 */
class SocialFeed extends AbstractModel implements SocialFeedInterface
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(SocialFeedResource::class);
    }

    /**
     * Get entity id
     *
     * @return string|null
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * Set entity id
     *
     * @param int|string $entityId
     * @return \BinaryAnvil\Federation\Model\SocialFeed
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Get key of social feed
     *
     * @return string|null
     */
    public function getKey()
    {
        return $this->getData(self::KEY);
    }

    /**
     * Set key of social feed
     *
     * @param string $key
     * @return \BinaryAnvil\Federation\Model\SocialFeed
     */
    public function setKey($key)
    {
        return $this->setData(self::KEY, $key);
    }

    /**
     * Get block class name
     *
     * @return string|null
     */
    public function getBlockClass()
    {
        return $this->getData(self::BLOCK_CLASS);
    }

    /**
     * Set block class name
     *
     * @param string $blockClass
     * @return \BinaryAnvil\Federation\Model\SocialFeed
     */
    public function setBlockClass($blockClass)
    {
        return $this->setData(self::BLOCK_CLASS, $blockClass);
    }

    /**
     * Get package (module name)
     * of social feed
     *
     * @return string|null
     */
    public function getPackage()
    {
        return $this->getData(self::PACKAGE);
    }

    /**
     * Set package (module name)
     * of social feed
     *
     * @param string $package
     * @return \BinaryAnvil\Federation\Model\SocialFeed
     */
    public function setPackage($package)
    {
        return $this->setData(self::PACKAGE, $package);
    }
}
