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
 * @package     BinaryAnvil_OrderLogix
 * @copyright   Copyright (c) 2017-2018 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\OrderLogix\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    const XML_PATH_IS_ENABLED = 'binaryanvil_orderlogix/general/is_enabled';
    const XML_PATH_DEBUG      = 'binaryanvil_orderlogix/general/debug';
    const XML_PATH_AUTHURL    = 'binaryanvil_orderlogix/general/authurl';
    const XML_PATH_AUTHUSER   = 'binaryanvil_orderlogix/general/authuser';
    const XML_PATH_AUTHPWD    = 'binaryanvil_orderlogix/general/authpwd';
    const XML_PATH_APIURL     = 'binaryanvil_orderlogix/general/apiurl';
    const XML_PATH_APIUSER    = 'binaryanvil_orderlogix/general/apiuser';
    const XML_PATH_APIPWD     = 'binaryanvil_orderlogix/general/apipwd';
    const XML_PATH_APITOKEN   = 'binaryanvil_orderlogix/general/apitoken';

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_IS_ENABLED);
    }

    /**
     * @return bool
     */
    public function isDebugEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_DEBUG);
    }

    /**
     * @return string
     */
    public function getAuthUrl()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AUTHURL);
    }

    /**
     * @return string
     */
    public function getAuthUser()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AUTHUSER);
    }

    /**
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AUTHPWD);
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_APIURL);
    }

    /**
     * @return string
     */
    public function getApiUser()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_APIUSER);
    }

    /**
     * @return string
     */
    public function getApiPassword()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_APIPWD);
    }

    /**
     * @return string
     */
    public function getApiToken()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_APITOKEN);
    }
}
