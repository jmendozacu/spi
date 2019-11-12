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
 * @package SizeChart
 * @copyright Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license http://www.binaryanvil.com/software/license
 */
namespace BinaryAnvil\SizeChart\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    /**
     * Is module enabled config path
     */
    const XML_PATH_IS_ENABLED = 'binaryanvil_sizechart/general/is_enabled';

    /**
     * Where show size chart block
     */
    const XML_PATH_SHOW_IN_ATTRIBUTE      = 'binaryanvil_sizechart/general/show_in_attribute';

    /**
     * Check if module enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_IS_ENABLED);
    }

    /**
     * Check where chow size chart block
     *
     * @return string
     */
    public function getShowInAttribute()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SHOW_IN_ATTRIBUTE);
    }
}
