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
use BinaryAnvil\Federation\Api\Data\SocialFeedItemInterface;
use BinaryAnvil\Federation\Api\SocialFeedItemRepositoryInterface;
use BinaryAnvil\Federation\Api\Data\SocialFeedItemSearchResultsInterfaceFactory;
use BinaryAnvil\Federation\Model\ResourceModel\SocialFeedItem as SocialFeedItemResource;
use BinaryAnvil\Federation\Model\ResourceModel\SocialFeedItem\CollectionFactory as SocialFeedItemCollectionFactory;

/**
 * Class SocialFeedItemRepository
 * @package BinaryAnvil\Federation\Model
 *
 * @codingStandardsIgnoreFile
 */
class SocialFeedItemRepository implements SocialFeedItemRepositoryInterface
{
    /**
     * @var \BinaryAnvil\Federation\Model\ResourceModel\SocialFeedItem
     */
    protected $resource;

    /**
     * @var \BinaryAnvil\Federation\Model\SocialFeedItemFactory
     */
    protected $socialFeedItemFactory;

    /**
     * @var \BinaryAnvil\Federation\Api\Data\SocialFeedItemSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \BinaryAnvil\Federation\Model\ResourceModel\SocialFeedItem\CollectionFactory
     */
    protected $socialFeedItemCollectionFactory;

    /**
     * SocialFeedItemRepository constructor.
     *
     * @param \BinaryAnvil\Federation\Model\SocialFeedItemFactory $socialFeedItemFactory
     * @param \BinaryAnvil\Federation\Model\ResourceModel\SocialFeedItem $socialFeedItemResource
     * @param \BinaryAnvil\Federation\Model\ResourceModel\SocialFeedItem\CollectionFactory $socialFeedItemCollectionFactory
     * @param \BinaryAnvil\Federation\Api\Data\SocialFeedItemSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        SocialFeedItemFactory $socialFeedItemFactory,
        SocialFeedItemResource $socialFeedItemResource,
        SocialFeedItemCollectionFactory $socialFeedItemCollectionFactory,
        SocialFeedItemSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $socialFeedItemResource;
        $this->socialFeedItemFactory = $socialFeedItemFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->socialFeedItemCollectionFactory = $socialFeedItemCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(SocialFeedItemInterface $item)
    {
        try {
            $this->resource->save($item);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the social feed item: %1',
                $exception->getMessage()
            ));
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($itemId)
    {
        $item = $this->socialFeedItemFactory->create();
        $this->resource->load($item, $itemId);

        if (!$item->getId()) {
            throw new NoSuchEntityException(__('Social feed item with id "%1" does not exist.', $itemId));
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->socialFeedItemCollectionFactory->create();

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
     * {@inheritdoc}
     */
    public function delete(SocialFeedItemInterface $item)
    {
        try {
            $this->resource->delete($item);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the social feed item row: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($itemId)
    {
        return $this->delete($this->getById($itemId));
    }
}
