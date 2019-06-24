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
 * @package     BinaryAnvil_Ratings
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\Ratings\Model;

use BinaryAnvil\Ratings\Api\Data\ReviewHelpfulInterface;
use BinaryAnvil\Ratings\Api\ReviewHelpfulRepositoryInterface;
use BinaryAnvil\Ratings\Model\ResourceModel\ReviewHelpful as ReviewHelpfulResource;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use BinaryAnvil\Ratings\Model\ResourceModel\ReviewHelpful\CollectionFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;
use BinaryAnvil\Ratings\Model\ReviewHelpfulFactory;

class ReviewHelpfulRepository implements ReviewHelpfulRepositoryInterface
{
    /**
     * @var \BinaryAnvil\Ratings\Model\ResourceModel\ReviewHelpful
     */
    protected $reviewHelpfulResource;

    /**
     * @var \BinaryAnvil\Ratings\Model\ResourceModel\ReviewHelpful\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \BinaryAnvil\Ratings\Model\ReviewHelpfulFactory
     */
    protected $reviewHelpfulFactory;

    /**
     * ReviewHelpfulRepository constructor.
     * @param \BinaryAnvil\Ratings\Model\ResourceModel\ReviewHelpful $reviewHelpfulResource
     * @param \BinaryAnvil\Ratings\Model\ResourceModel\ReviewHelpful\CollectionFactory $collectionFactory
     * @param \BinaryAnvil\Ratings\Model\ReviewHelpfulFactory $reviewHelpfulFactory
     */
    public function __construct(
        ReviewHelpfulResource $reviewHelpfulResource,
        CollectionFactory $collectionFactory,
        ReviewHelpfulFactory $reviewHelpfulFactory
    ) {
        $this->reviewHelpfulResource = $reviewHelpfulResource;
        $this->collectionFactory = $collectionFactory;
        $this->reviewHelpfulFactory = $reviewHelpfulFactory;
    }

    /**
     * @param \BinaryAnvil\Ratings\Api\Data\ReviewHelpfulInterface $reviewHelpful
     * @return \BinaryAnvil\Ratings\Api\Data\ReviewHelpfulInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(ReviewHelpfulInterface $reviewHelpful)
    {
        try {
            $this->reviewHelpfulResource->save($reviewHelpful);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the review helpful',
                $exception->getMessage()
            ));
        }
        return $reviewHelpful;
    }

    /**
     * @param int $reviewHelpfulId
     * @return \BinaryAnvil\Ratings\Api\Data\ReviewHelpfulInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($reviewHelpfulId)
    {
        /** @var \BinaryAnvil\Ratings\Model\ReviewHelpful $reviewHelpfulVote */
        $reviewHelpfulVote = $this->reviewHelpfulFactory->create();

        $this->reviewHelpfulResource->load($reviewHelpfulVote, $reviewHelpfulId);
        if (!$reviewHelpfulVote->getId()) {
            throw new NoSuchEntityException(__('Helpful Vote with id "%1" does not exist.', $reviewHelpfulId));
        }

        return $reviewHelpfulVote;
    }

    /**
     * Retrieve PromoItem matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \BinaryAnvil\Ratings\Api\Data\ReviewHelpfulInterface[]
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \BinaryAnvil\Ratings\Model\ResourceModel\ReviewHelpful\Collection $collection */
        $collection = $this->collectionFactory->create();

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];

            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }

            $collection->addFieldToFilter($fields, $conditions);
        }

        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    $sortOrder->getDirection() ? : SortOrder::SORT_ASC
                );
            }
        }

        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        return $collection->getItems();
    }
}
