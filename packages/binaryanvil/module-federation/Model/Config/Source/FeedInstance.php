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

namespace BinaryAnvil\Federation\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use BinaryAnvil\Federation\Model\SocialFeedRepository;

/**
 * Class FeedInstance
 * @package BinaryAnvil\Federation\Model\Config\Source
 *
 * Source model for social feeds widget (field 'feed_instance_list')
 */
class FeedInstance implements ArrayInterface
{
    /**
     * @var \BinaryAnvil\Federation\Model\SocialFeedRepository
     */
    private $socialFeedRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * FeedInstance source model constructor
     *
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \BinaryAnvil\Federation\Model\SocialFeedRepository $socialFeedRepository
     */
    public function __construct(
        SocialFeedRepository $socialFeedRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->socialFeedRepository = $socialFeedRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Retrieve array of available
     * social feed instance
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $feedInstanceList = $this->socialFeedRepository->getList($searchCriteria);

        foreach ($feedInstanceList->getItems() as $feedInstance) {
            /** @var $feedInstance \BinaryAnvil\Federation\Model\SocialFeed */
            $optionArray[] = ['value' => $feedInstance->getKey(), 'label' => ucfirst($feedInstance->getKey())];
        }

        return $optionArray;
    }
}
