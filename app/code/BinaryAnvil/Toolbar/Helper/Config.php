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
 * @package     BinaryAnvil_Toolbar
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\Toolbar\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    /**#@+
     *
     * Toolbar system configurations paths
     *
     * @type string
     */
    const XML_PATH_TOOLBAR_SORT_BY_ACTION   = 'binaryanvil_toolbar/sort_by/action';
    const XML_PATH_TOOLBAR_SORT_BY_INSERT   = 'binaryanvil_toolbar/sort_by/insert';
    const XML_PATH_TOOLBAR_SORT_BY_OPTIONS  = 'binaryanvil_toolbar/sort_by/options';
    /**#@- */

    /**
     * Retrieve custom sorting action (behavior)
     *
     * @return int|null
     */
    public function getSortByAction()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TOOLBAR_SORT_BY_ACTION);
    }

    /**
     * Retrieve custom sorting place
     * (relative to native sorting)
     *
     * @return int|null
     */
    public function getSortByInsertTo()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TOOLBAR_SORT_BY_INSERT);
    }

    /**
     * Retrieve custom sorting options
     *
     * @return string|null
     */
    public function getSortByOptions()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TOOLBAR_SORT_BY_OPTIONS);
    }
}
