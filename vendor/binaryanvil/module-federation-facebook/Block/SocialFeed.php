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

use BinaryAnvil\Federation\Api\SocialFeedManagementInterface;
use BinaryAnvil\Federation\Model\Flag;
use BinaryAnvil\Federation\Model\FlagFactory;
use Magento\Framework\View\Element\Template;
use BinaryAnvil\Federation\Block\SocialFeeds;
use BinaryAnvil\FederationFacebook\Model\Facebook\Config;

/**
 * Class SocialFeed
 * @package BinaryAnvil\FederationFacebook\Block
 */
class SocialFeed extends Template
{
    /**
     * Key for 'binaryanvil_social_instance' table
     *
     * @see \BinaryAnvil\Federation\Setup\UpgradeSchema::installSocialFeedInstanceTable()
     */
    const SOCIAL_FEED_KEY = 'facebook';

    /**
     * @var \BinaryAnvil\FederationFacebook\Model\Facebook\Config
     */
    protected $facebookConfig;

    /**
     * @codingStandardsIgnoreStart
     */
    /**
     * @var string Base template
     */
    protected $_template = 'social-feed/facebook.phtml';
    /**
     * @codingStandardsIgnoreEnd
     */

    /**
     * @var \BinaryAnvil\Federation\Api\SocialFeedManagementInterface
     */
    protected $socialFeedManagement;

    /**
     * @var \BinaryAnvil\Federation\Model\FlagFactory
     */
    protected $flagFactory;

    /**
     * SocialFeed constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \BinaryAnvil\FederationFacebook\Model\Facebook\Config $facebookConfig
     * @param \BinaryAnvil\Federation\Api\SocialFeedManagementInterface $socialFeedManagement
     * @param \BinaryAnvil\Federation\Model\FlagFactory $flagFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $facebookConfig,
        SocialFeedManagementInterface $socialFeedManagement,
        FlagFactory $flagFactory,
        array $data = []
    ) {
        $this->facebookConfig = $facebookConfig;
        $this->socialFeedManagement = $socialFeedManagement;
        $this->flagFactory = $flagFactory;

        parent::__construct($context, $data);
    }

    /**
     * Retrieve parent selector
     * (if parent exist)
     *
     * @return string
     */
    public function getParentSelector()
    {
        $parent = $this->getParentBlock();

        if ($parent) {
            return '.' . $parent->getCssClass() . ' ';
        }

        return '';
    }

    /**
     * Retrieve account property
     * for JSON object
     *
     * @return string
     */
    public function getAccount()
    {
        $mode = $this->facebookConfig->getFeedMode();

        if ($mode) {
            $body = '';

            if ($mode == Config::USER_FEED_MODE_SYMBOL) {
                if ($userName = $this->facebookConfig->getUserName()) {
                    $body = $userName;
                }
            } elseif ($mode == Config::PAGE_FEED_MODE_SYMBOL) {
                if ($pageId = $this->facebookConfig->getPageId()) {
                    $body = $pageId;
                }
            }

            if ($body) {
                return $mode . $body;
            }
        }

        return '';
    }

    /**
     * Retrieve facebook posts limit
     *
     * @return int
     */
    public function getLimit()
    {
        if ($limit = (int) $this->facebookConfig->getPostsLimit()) {
            return $limit;
        }

        return (int) SocialFeeds::DEFAULT_FEEDS_LIMIT;
    }

    /**
     * Retrieve access token
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->facebookConfig->getApiAccessToken();
    }

    /**
     * Flag for section render
     *
     * @return bool
     */
    public function isFeedNeeded()
    {
        return $this->facebookConfig->isFacebookFeedEnabled() && $this->getAccessToken() && $this->getAccount();
    }

    /**
     * Retrieve JSON with cached posts
     *
     * @return string|array
     */
    public function getCachedPosts()
    {
        if (!$this->facebookConfig->isCacheEnabled()) {
            return [];
        }
        return json_encode($this->socialFeedManagement->getPostsByKey(self::SOCIAL_FEED_KEY));
    }

    /**
     * Check if cache expired
     *
     * @return bool
     */
    public function isCacheExpired()
    {
        $flag = $this->flagFactory->create();
        $flag->setSocialCacheFlagCode(Flag::FLAG_PREFIX_CACHE_SOCIAL_FEED_INSTANCE . self::SOCIAL_FEED_KEY)
            ->loadSelf();

        $isCacheEnabled = $this->facebookConfig->isCacheEnabled();
        $isCacheExpired = !$flag->hasData() ?: false;

        $data = json_decode($flag->getFlagData(), true);
        if (!$isCacheExpired && isset($data['time'])) {
            $lastUpdate = $data['time'];
            $timePassed = time() - $lastUpdate;
            $isCacheExpired = $timePassed >= $this->facebookConfig->getCacheLifetime() ?: false;
        }

        return $isCacheEnabled && $isCacheExpired;
    }
}
