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

namespace BinaryAnvil\ExtendFederationFacebook\Plugin\FederationFacebook\Model;

use Magento\Framework\App\Request\Http;
use BinaryAnvil\FederationFacebook\Model\Facebook as OriginalClass;

class Facebook
{
    /**
     * (string) New param code (for PersistentDataHandler)
     */
    const EXTEND_PARAM_CODE = 'state';

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * Facebook plugin constructor
     *
     * @param Http $request
     */
    public function __construct(Http $request)
    {
        $this->request = $request;
    }

    /**
     * Set new params to persistent data object
     *
     * @see   \BinaryAnvil\FederationFacebook\Model\Facebook::getRedirectLoginHelper()
     * @param \BinaryAnvil\FederationFacebook\Model\Facebook $subject
     * @param  callable $proceed
     * @return \Facebook\Helpers\FacebookRedirectLoginHelper
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetRedirectLoginHelper(OriginalClass $subject, callable $proceed)
    {
        /** @var \Facebook\Helpers\FacebookRedirectLoginHelper $facebookRedirectLoginHelper */
        $facebookRedirectLoginHelper = $proceed();

        if ($state = $this->request->getParam(self::EXTEND_PARAM_CODE, '')) {
            $facebookRedirectLoginHelper->getPersistentDataHandler()->set(
                self::EXTEND_PARAM_CODE,
                $state
            );
        }

        return $facebookRedirectLoginHelper;
    }
}
