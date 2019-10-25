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

namespace BinaryAnvil\Federation\Model\ResourceModel;

use BinaryAnvil\Federation\Api\Data\SocialFeedItemInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class SocialFeedItem
 * @package BinaryAnvil\Federation\Model\ResourceModel
 *
 * Define resource model for Social Feeds Items
 *
 * @codingStandardsIgnoreFile
 */
class SocialFeedItem extends AbstractDb
{
    /**
     * SocialFeedItem entity table name
     */
    const DB_SCHEMA_TABLE_ENTITY_NAME = 'binaryanvil_social_instance_item';

    /**
     * Init resource model for SocialFeedItem entity
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::DB_SCHEMA_TABLE_ENTITY_NAME, SocialFeedItemInterface::ITEM_ID);
    }
}
