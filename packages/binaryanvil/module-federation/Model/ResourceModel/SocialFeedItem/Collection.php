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

namespace BinaryAnvil\Federation\Model\ResourceModel\SocialFeedItem;

use BinaryAnvil\Federation\Model\SocialFeedItem as SocialFeedItemBusiness;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use BinaryAnvil\Federation\Model\ResourceModel\SocialFeedItem as SocialFeedItemResource;

/**
 * Class Collection
 * @package BinaryAnvil\Federation\Model\ResourceModel\SocialFeedItem
 *
 * Collection for Social Feed Items
 *
 * @codingStandardsIgnoreFile
 */
class Collection extends AbstractCollection
{
    /**
     * Init social feed items collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(SocialFeedItemBusiness::class, SocialFeedItemResource::class);
    }
}
