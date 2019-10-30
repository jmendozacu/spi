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

use BinaryAnvil\FederationFacebook\Model\Facebook as FacebookModel;
use BinaryAnvil\FederationFacebook\Model\Facebook\Config;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session;

class Facebook extends Template
{

    /**
     * @var \BinaryAnvil\FederationFacebook\Model\Facebook\Config $config
     */
    protected $config;

    /**
     * @var \BinaryAnvil\FederationFacebook\Model\Facebook $facebook
     */
    protected $facebook;

    /**
     * @var \Magento\Customer\Model\Session $session
     */
    private $session;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     * @param \BinaryAnvil\FederationFacebook\Model\Facebook\Config $config
     * @param FacebookModel $facebook
     * @param \Magento\Customer\Model\Session $session
     */
    public function __construct(
        Context $context,
        array $data,
        Config $config,
        FacebookModel $facebook,
        Session $session
    ) {
        $this->config = $config;
        $this->facebook = $facebook;
        $this->session = $session;

        parent::__construct($context, $data);
    }

    /**
     * Check if extension is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->config->isEnabled();
    }

    /**
     * Retrieve facebook login URL
     *
     * @return string
     */
    public function getLoginUrl()
    {
        $facebookHelper = $this->facebook->getRedirectLoginHelper();

        return $facebookHelper->getLoginUrl($this->getUrl('facebook/login'), ['scope' => 'email']);
    }

    /**
     * Check if customer is logged in
     *
     * @return boolean
     */
    public function isLoggedIn()
    {
        return $this->session->isLoggedIn();
    }
}
