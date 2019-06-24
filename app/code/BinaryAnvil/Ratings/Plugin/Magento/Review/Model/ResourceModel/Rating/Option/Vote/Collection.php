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

namespace BinaryAnvil\Ratings\Plugin\Magento\Review\Model\ResourceModel\Rating\Option\Vote;

use Magento\Review\Model\ResourceModel\Rating\Option\Vote\Collection as OriginalClass;
use Magento\Store\Model\StoreManagerInterface;

class Collection
{
    /**
     * Store list manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Collection constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Review\Model\ResourceModel\Rating\Option\Vote\Collection $subject
     * @param callable $proceed
     * @param null $storeId
     * @return \Magento\Review\Model\ResourceModel\Rating\Option\Vote\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundAddRatingInfo(OriginalClass $subject, callable $proceed, $storeId = null)
    {
        $connection = $subject->getConnection();
        $ratingCodeCond = $connection->getIfNullSql('title.value', 'rating.rating_code');
        $subject->getSelect()->join(
            ['rating' => $subject->getTable('rating')],
            'rating.rating_id = main_table.rating_id',
            ['rating_code', 'is_used_in_summary']
        )->joinLeft(
            ['title' => $subject->getTable('rating_title')],
            $connection->quoteInto(
                'main_table.rating_id=title.rating_id AND title.store_id = ?',
                (int)$this->storeManager->getStore()->getId()
            ),
            ['rating_code' => $ratingCodeCond]
        );
        if (!$this->storeManager->isSingleStoreMode()) {
            if ($storeId == null) {
                $storeId = $this->storeManager->getStore()->getId();
            }

            if (is_array($storeId)) {
                $condition = $connection->prepareSqlCondition('store.store_id', ['in' => $storeId]);
            } else {
                $condition = $connection->quoteInto('store.store_id = ?', $storeId);
            }

            $subject->getSelect()->join(
                ['store' => $subject->getTable('rating_store')],
                'main_table.rating_id = store.rating_id AND ' . $condition
            );
        }
        $connection->fetchAll($subject->getSelect());
        return $subject;
    }
}
