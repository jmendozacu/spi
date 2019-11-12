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

namespace BinaryAnvil\FederationFacebook\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class LikeWidget extends Template
{
    /**
     * @codingStandardsIgnoreStart
     * @var string $_template
     */
    protected $_template = 'BinaryAnvil_FederationFacebook::like-widget.phtml';
    /**
     * @codingStandardsIgnoreEnd
     */

    const XML_PATH_IS_ENABLED = 'binaryanvil_federation/facebook_like/is_enabled';

    /**
     * Check if module is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getConfig(static::XML_PATH_IS_ENABLED);
    }

    /**
     * Get store config
     *
     * @param string $path
     * @param string $scopeType
     * @param null $store
     *
     * @return string
     */
    public function getConfig($path, $scopeType = ScopeInterface::SCOPE_STORE, $store = null)
    {
        if ($store === null) {
            $store = $this->_storeManager->getStore()->getId();
        }

        return $this->_scopeConfig->getValue(
            $path,
            $scopeType,
            $store
        );
    }
}
