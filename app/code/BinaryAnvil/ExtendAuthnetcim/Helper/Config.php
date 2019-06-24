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
 * @package     ExtendAuthnetcim
 * @copyright   Copyright (c) 2017-2018 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\ExtendAuthnetcim\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    /**
     * Is OrderLogix transaction enabled
     */
    const XML_PATH_ORDERLOGIX_TRANSACTION_ENABLED = 'payment/authnetcim/orderlogix_transaction_enabled';

    /**
     * Transaction amount to Auth.net
     */
    const XML_PATH_ORDERLOGIX_TRANSACTION_AMOUNT = 'payment/authnetcim/orderlogix_transaction_amount';

    /**
     * Check if OrderLogix transaction enabled
     *
     * @return bool
     */
    public function isOrderLogixTransactionEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ORDERLOGIX_TRANSACTION_ENABLED);
    }

    /**
     * Retrieve OrderLogix transaction enabled amount
     *
     * @return string
     */
    public function getOrderLogixTransactionAmount()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ORDERLOGIX_TRANSACTION_AMOUNT);
    }
}
