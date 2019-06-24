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

namespace BinaryAnvil\Ratings\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use BinaryAnvil\Ratings\Model\ReviewHelpfulRepository;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use BinaryAnvil\Ratings\Api\Data\ReviewHelpfulInterface;
use BinaryAnvil\Ratings\Model\ReviewHelpfulFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Review\Model\ResourceModel\Rating\CollectionFactory as RatingCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use IntlDateFormatter;

class Data extends AbstractHelper
{
    /**
     * @var \BinaryAnvil\Ratings\Model\ReviewHelpfulRepository
     */
    protected $reviewHelpfulRepository;

    /**
     * @var \BinaryAnvil\Ratings\Model\ReviewHelpfulFactory
     */
    protected $reviewHelpfulFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\SortOrder
     */
    protected $sortOrder;

    /**
     * @var \Magento\Review\Model\ResourceModel\Rating\CollectionFactory
     */
    protected $ratingCollectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timeZone;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \BinaryAnvil\Ratings\Model\ReviewHelpfulRepository $reviewHelpfulRepository
     * @param \BinaryAnvil\Ratings\Model\ReviewHelpfulFactory $reviewHelpfulFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Review\Model\ResourceModel\Rating\CollectionFactory $ratingCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        Context $context,
        ReviewHelpfulRepository $reviewHelpfulRepository,
        ReviewHelpfulFactory $reviewHelpfulFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RatingCollectionFactory $ratingCollectionFactory,
        StoreManagerInterface $storeManager,
        TimezoneInterface $timezone
    ) {
        $this->reviewHelpfulRepository = $reviewHelpfulRepository;
        $this->reviewHelpfulFactory = $reviewHelpfulFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->ratingCollectionFactory = $ratingCollectionFactory;
        $this->storeManager = $storeManager;
        $this->timeZone = $timezone;

        parent::__construct($context);
    }

    /**
     * @param int $reviewId
     * @return array
     */
    public function getReviewHelpfulVotesCount($reviewId)
    {
        /** @var \Magento\Framework\Api\SearchCriteria $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(ReviewHelpfulInterface::REVIEW_ID, $reviewId)
            ->create();
        /** @var \BinaryAnvil\Ratings\Api\Data\ReviewHelpfulInterface[] $helpfuls */
        $helpfuls = $this->reviewHelpfulRepository->getList($searchCriteria);
        $commonCount = count($helpfuls);

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(ReviewHelpfulInterface::REVIEW_ID, $reviewId)
            ->addFilter(ReviewHelpfulInterface::IS_HELPFUL, 1)
            ->create();
        $helpfuls = $this->reviewHelpfulRepository->getList($searchCriteria);
        $positiveCount = count($helpfuls);

        return ['positive' => $positiveCount, 'negative' => $commonCount - $positiveCount];
    }

    /**
     * @param int $reviewId
     * @param int $customerId
     * @return \BinaryAnvil\Ratings\Api\Data\ReviewHelpfulInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomerHelpfulByReviewId($reviewId, $customerId, $helpfulVotes)
    {
        /** @var \BinaryAnvil\Ratings\Api\Data\ReviewHelpfulInterface $helpful */
        $helpful = $this->reviewHelpfulFactory->create();
        $helpful->setReviewId($reviewId)->setCustomerId($customerId);
        if ($customerId) {
            /** @var \Magento\Framework\Api\SearchCriteria $searchCriteria */
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(ReviewHelpfulInterface::CUSTOMER_ID, $helpful->getCustomerId())
                ->addFilter(ReviewHelpfulInterface::REVIEW_ID, $helpful->getReviewId())
                ->create();
            /** @var \BinaryAnvil\Ratings\Api\Data\ReviewHelpfulInterface[] $helpfuls */
            $helpfuls = $this->reviewHelpfulRepository->getList($searchCriteria);

            if ($helpfuls) {
                return array_shift($helpfuls);
            }

            return $helpful;
        }

        $customerHelpfulVotes = $helpfulVotes ?? [];
        if (array_key_exists($helpful->getReviewId(), $customerHelpfulVotes)) {
            $helpful = $this->reviewHelpfulRepository->getById($customerHelpfulVotes[$helpful->getReviewId()]);
        }

        return $helpful;
    }

    /**
     * @param $date
     * @param int $format
     * @return string
     */
    public function getFormattedDate($date, $format = IntlDateFormatter::SHORT)
    {
        return $this->timeZone->formatDate($date, $format);
    }

    /**
     * @param int $isUsedInSummary
     * @return \Magento\Review\Model\ResourceModel\Rating\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRatingCollection($isUsedInSummary)
    {
        return $this->ratingCollectionFactory->create()
            ->setPositionOrder()
            ->setStoreFilter($this->storeManager->getStore()->getId())
            ->addRatingPerStoreName($this->storeManager->getStore()->getId())
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('is_used_in_summary', $isUsedInSummary);
    }
}