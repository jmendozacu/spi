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
 * @package     Infinity
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */
/**
 * @codingStandardsIgnoreFile
 */
namespace BinaryAnvil\Ratings\Preference\Magento\Review\Model\ResourceModel\Review;

use Magento\Review\Model\ResourceModel\Review\Collection as OriginClass;
use BinaryAnvil\Ratings\Preference\Magento\Review\Model\Review as ReviewModel;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Data\Collection\EntityFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Review\Helper\Data;
use Magento\Review\Model\Rating\Option\VoteFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use BinaryAnvil\Ratings\Api\Data\ReviewHelpfulInterface;
use BinaryAnvil\Ratings\Model\ReviewHelpfulFactory;
use BinaryAnvil\Ratings\Model\ReviewHelpfulRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends OriginClass
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \BinaryAnvil\Ratings\Model\ReviewHelpfulFactory
     */
    protected $reviewHelpfulFactory;

    /**
     * @var \BinaryAnvil\Ratings\Model\ReviewHelpfulRepository
     */
    protected $reviewHelpfulRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Review\Helper\Data $reviewData
     * @param \Magento\Review\Model\Rating\Option\VoteFactory $voteFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \BinaryAnvil\Ratings\Model\ReviewHelpfulFactory $reviewHelpfulFactory
     * @param \BinaryAnvil\Ratings\Model\ReviewHelpfulRepository $reviewHelpfulRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        EntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Data $reviewData,
        VoteFactory $voteFactory,
        StoreManagerInterface $storeManager,
        CustomerSession $customerSession,
        ReviewHelpfulFactory $reviewHelpfulFactory,
        ReviewHelpfulRepository $reviewHelpfulRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->customerSession = $customerSession;
        $this->reviewHelpfulFactory = $reviewHelpfulFactory;
        $this->reviewHelpfulRepository = $reviewHelpfulRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;

        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $reviewData,
            $voteFactory,
            $storeManager,
            $connection,
            $resource
        );
    }

    /**
     * Initialize select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->join(
            ['detail2' => $this->getReviewDetailTable()],
            'main_table.review_id = detail2.review_id',
            [ReviewModel::IS_RECOMMEND_PRODUCT]
        );

        return $this;
    }

    protected function _afterLoad()
    {
        /** @var \BinaryAnvil\Ratings\Preference\Magento\Review\Model\Review $item */
        foreach ($this->_items as $item) {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(ReviewHelpfulInterface::REVIEW_ID, $item->getId())
                ->addFilter(ReviewHelpfulInterface::IS_HELPFUL, 1)
                ->create();
            /** @var \BinaryAnvil\Ratings\Api\Data\ReviewHelpfulInterface[] $helpfuls */
            $positiveHelpfuls = $this->reviewHelpfulRepository->getList($searchCriteria);

            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(ReviewHelpfulInterface::REVIEW_ID, $item->getId())
                ->addFilter(ReviewHelpfulInterface::IS_HELPFUL, 0)
                ->create();
            /** @var \BinaryAnvil\Ratings\Api\Data\ReviewHelpfulInterface[] $helpfuls */
            $negativeHelpfuls = $this->reviewHelpfulRepository->getList($searchCriteria);

            $item->addData([
                    'positive_helpful_vote_count' => count($positiveHelpfuls),
                    'negative_helpful_vote_count' => count($negativeHelpfuls)
                ]
            );
        }
        return parent::_afterLoad();
    }
}
