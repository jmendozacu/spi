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
 * @package     BinaryAnvil_FederationFacebook
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\FederationFacebook\Model\Source\Config;

use Magento\Framework\Option\ArrayInterface;
use BinaryAnvil\FederationFacebook\Model\Facebook\Config;

/**
 * Class FeedMode
 * @package BinaryAnvil\FederationFacebook\Model\Source\Config
 *
 * Source model for 'Feed Mode' system configuration
 */
class FeedMode implements ArrayInterface
{
    /**
     * Retrieve array of available modes
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => Config::USER_FEED_MODE_SYMBOL, 'label' => __('User')],
            ['value' => Config::PAGE_FEED_MODE_SYMBOL, 'label' => __('Page')],
        ];
    }
}
