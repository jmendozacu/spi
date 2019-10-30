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
 * @package     BinaryAnvil_Federation
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\Federation\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Class SocialFeedRepositoryInterface
 * @package BinaryAnvil\Federation\Api
 */
interface SocialFeedRepositoryInterface
{
    /**
     * Save social feed
     *
     * @param  \BinaryAnvil\Federation\Api\Data\SocialFeedInterface $socialFeed
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\SocialFeedInterface $socialFeed);

    /**
     * Get social feed data by instance key
     *
     * @param string $socialFeedKey
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($socialFeedKey);

    /**
     * Retrieve social feeds matching the specified criteria.
     *
     * @param  \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete social feed
     *
     * @param  \BinaryAnvil\Federation\Api\Data\SocialFeedInterface $socialFeed
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\SocialFeedInterface $socialFeed);

    /**
     * Delete Social Feed by instance key
     *
     * @return bool true on success
     * @param  string $socialFeedKey
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteByKey($socialFeedKey);
}
