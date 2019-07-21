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

namespace BinaryAnvil\Federation\Model;

use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use BinaryAnvil\Federation\Api\Data\SocialFeedInterface;
use BinaryAnvil\Federation\Api\SocialFeedRepositoryInterface;
use BinaryAnvil\Federation\Api\Data\SocialFeedSearchResultsInterfaceFactory;
use BinaryAnvil\Federation\Model\ResourceModel\SocialFeed as SocialFeedResource;
use BinaryAnvil\Federation\Model\ResourceModel\SocialFeed\CollectionFactory as SocialFeedCollectionFactory;

/**
 * Class SocialFeedRepository
 * @package BinaryAnvil\Federation\Model
 */
class SocialFeedRepository implements SocialFeedRepositoryInterface
{
    /**
     * @var \BinaryAnvil\Federation\Model\ResourceModel\SocialFeed
     */
    protected $resource;

    /**
     * @var \BinaryAnvil\Federation\Model\SocialFeedFactory
     */
    protected $socialFeedFactory;

    /**
     * @var \BinaryAnvil\Federation\Api\Data\SocialFeedSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \BinaryAnvil\Federation\Model\ResourceModel\SocialFeed\CollectionFactory
     */
    protected $socialFeedCollectionFactory;

    /**
     * SocialFeedRepository constructor
     *
     * @param \BinaryAnvil\Federation\Model\SocialFeedFactory $socialFeedFactory
     * @param \BinaryAnvil\Federation\Model\ResourceModel\SocialFeed $socialFeedResource
     * @param \BinaryAnvil\Federation\Api\Data\SocialFeedSearchResultsInterfaceFactory $searchResultsFactory
     * @param \BinaryAnvil\Federation\Model\ResourceModel\SocialFeed\CollectionFactory $socialFeedCollectionFactory
     */
    public function __construct(
        SocialFeedFactory $socialFeedFactory,
        SocialFeedResource $socialFeedResource,
        SocialFeedCollectionFactory $socialFeedCollectionFactory,
        SocialFeedSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $socialFeedResource;
        $this->socialFeedFactory = $socialFeedFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->socialFeedCollectionFactory = $socialFeedCollectionFactory;
    }

    /**
     * Save Social Feed
     *
     * @param  \BinaryAnvil\Federation\Api\Data\SocialFeedInterface $socialFeed
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(SocialFeedInterface $socialFeed)
    {
        try {
            $this->resource->save($socialFeed);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the social feed: %1',
                $exception->getMessage()
            ));
        }

        return $socialFeed;
    }

    /**
     * Get social feed data by instance key
     *
     * @param string $socialFeedKey
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($socialFeedKey)
    {
        $socialFeed = $this->socialFeedFactory->create();
        $this->resource->load($socialFeed, $socialFeedKey, SocialFeedInterface::KEY);

        if (!$socialFeed->getId()) {
            throw new NoSuchEntityException(__('Social feed with instance key "%1" does not exist.', $socialFeedKey));
        }

        return $socialFeed;
    }

    /**
     * Retrieve Social Feeds matching the specified criteria
     *
     * @param  \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    ) {
        $collection = $this->socialFeedCollectionFactory->create();

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? SortOrder::SORT_ASC : SortOrder::SORT_DESC
                );
            }
        }

        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    /**
     * Delete Social Feed
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     * @param  \BinaryAnvil\Federation\Api\Data\SocialFeedInterface $socialFeed
     */
    public function delete(SocialFeedInterface $socialFeed)
    {
        try {
            $this->resource->delete($socialFeed);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the social feed row: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }

    /**
     * Delete Social Feed by instance key
     *
     * @return bool true on success
     * @param  string $socialFeedKey
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteByKey($socialFeedKey)
    {
        return $this->delete($this->get($socialFeedKey));
    }
}
