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
 * @package     BinaryAnvil_ExtendFederationFacebook
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\ExtendFederationFacebook\Plugin\FederationFacebook\Controller\Login;

use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use BinaryAnvil\FederationFacebook\Controller\Login\Index as OriginalAction;

class Index
{
    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * Index action plugin constructor
     *
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    /**
     * Cleanup cookie storage after facebook log in
     *
     * @param  \BinaryAnvil\FederationFacebook\Controller\Login\Index::execute() $subject
     * @param  $result
     * @return mixed
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(OriginalAction $subject, $result)
    {
        if ($this->cookieManager->getCookie('mage-cache-sessid')) {
            $metadata = $this->cookieMetadataFactory->createCookieMetadata();
            $metadata->setPath('/');
            $this->cookieManager->deleteCookie('mage-cache-sessid', $metadata);
        }

        return $result;
    }
}
