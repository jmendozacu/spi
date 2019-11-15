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

namespace BinaryAnvil\FederationFacebook\Model\Facebook;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    /**
     * Get a facebook user data
     *
     * @see https://developers.facebook.com/docs/graph-api/reference/user
     */
    const FACEBOOK_FIELDS = 'id,email,first_name,last_name,middle_name,gender,picture';

    const ATTR_FACEBOOK_ID      = 'facebook_id';
    const ATTR_FACEBOOK_TOKEN   = 'facebook_token';

    const XML_PATH_API_KEY      = 'binaryanvil_federation/facebook/api_key';
    const XML_PATH_IS_ENABLED   = 'binaryanvil_federation/facebook/is_enabled';
    const XML_PATH_API_SECRET   = 'binaryanvil_federation/facebook/api_secret';
    const XML_PATH_ACCESS_TOKEN = 'binaryanvil_federation/facebook/api_access_token';

    const XML_PATH_FEED_ENABLED             = 'binaryanvil_federation/facebook_social_feed/is_enabled';
    const XML_PATH_POSTS_LIMIT              = 'binaryanvil_federation/facebook_social_feed/posts_limit';
    const XML_PATH_FEED_MODE                = 'binaryanvil_federation/facebook_social_feed/feed_mode';
    const XML_PATH_PAGE_ID                  = 'binaryanvil_federation/facebook_social_feed/page_id';
    const XML_PATH_USER_NAME                = 'binaryanvil_federation/facebook_social_feed/user';
    const XML_PATH_FEED_IS_CACHE_ENABLED    = 'binaryanvil_federation/facebook_social_feed/is_cache_enabled';
    const XML_PATH_FEED_CACHE_LIFETIME      = 'binaryanvil_federation/facebook_social_feed/cache_lifetime';

    const VERSION = 'v2.2';

    /**#@+
     *
     * Facebook Available Modes
     *
     * @type string
     */
    const USER_FEED_MODE_SYMBOL = '@';
    const PAGE_FEED_MODE_SYMBOL = '!';
    /**#@- */

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     */
    public function __construct(ScopeConfigInterface $scopeConfigInterface)
    {
        $this->scopeConfigInterface = $scopeConfigInterface;
    }

    /**
     * Get API key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->scopeConfigInterface->getValue(self::XML_PATH_API_KEY);
    }

    /**
     * Get API Secret
     *
     * @return string
     */
    public function getApiSecret()
    {
        return $this->scopeConfigInterface->getValue(self::XML_PATH_API_SECRET);
    }

    /**
     * Get access token
     *
     * @return string
     */
    public function getApiAccessToken()
    {
        return $this->scopeConfigInterface->getValue(self::XML_PATH_ACCESS_TOKEN);
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * Check if module is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfigInterface->getValue(self::XML_PATH_IS_ENABLED);
    }

    /**
     * Get limit of posts in feed
     *
     * @return string
     */
    public function getPostsLimit()
    {
        return $this->scopeConfigInterface->getValue(self::XML_PATH_POSTS_LIMIT);
    }

    /**
     * Get feed mode
     *
     * @return string
     */
    public function getFeedMode()
    {
        return $this->scopeConfigInterface->getValue(self::XML_PATH_FEED_MODE);
    }

    /**
     * Get facebook page id
     * for feed
     *
     * @return string
     */
    public function getPageId()
    {
        return $this->scopeConfigInterface->getValue(self::XML_PATH_PAGE_ID);
    }

    /**
     * Get facebook user name
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->scopeConfigInterface->getValue(self::XML_PATH_USER_NAME);
    }

    /**
     * Flag facebook social feed
     * is enabled
     *
     * @return bool
     */
    public function isFacebookFeedEnabled()
    {
        return (bool) $this->scopeConfigInterface->getValue(self::XML_PATH_FEED_ENABLED);
    }

    /**
     * Check if cache enabled
     *
     * @return bool
     */
    public function isCacheEnabled()
    {
        return $this->scopeConfigInterface->isSetFlag(self::XML_PATH_FEED_IS_CACHE_ENABLED);
    }

    /**
     * Retrieve cache lifetime
     *
     * @return string
     */
    public function getCacheLifetime()
    {
        return $this->scopeConfigInterface->getValue(self::XML_PATH_FEED_CACHE_LIFETIME);
    }
}
