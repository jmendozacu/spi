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
 * Interface SocialFeedItemRepositoryInterface
 * @package BinaryAnvil\Federation\Api
 */
interface SocialFeedItemRepositoryInterface
{
    /**
     * Save social feed item
     *
     * @param  \BinaryAnvil\Federation\Api\Data\SocialFeedItemInterface $item
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\SocialFeedItemInterface $item);

    /**
     * Get social feed item data by ID
     *
     * @param int $itemId
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($itemId);

    /**
     * Retrieve social feed items matching the specified criteria.
     *
     * @param  \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedItemSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete social feed item
     *
     * @param  \BinaryAnvil\Federation\Api\Data\SocialFeedItemInterface $item
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\SocialFeedItemInterface $item);

    /**
     * Delete social feed item by ID
     *
     * @param int $itemId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($itemId);
}
