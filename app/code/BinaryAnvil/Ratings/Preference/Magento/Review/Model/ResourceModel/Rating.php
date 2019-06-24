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
/** @codingStandardsIgnoreFile */

namespace BinaryAnvil\Ratings\Preference\Magento\Review\Model\ResourceModel;

use Magento\Review\Model\ResourceModel\Rating as OriginalClass;

class Rating extends OriginalClass
{
    /**
     * Return data of rating summary
     *
     * @param \Magento\Review\Model\Rating $object
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getEntitySummaryData($object)
    {
        $connection = $this->getConnection();

        $sumColumn = new \Zend_Db_Expr("SUM(rating_vote.{$connection->quoteIdentifier('percent')})");
        $countColumn = new \Zend_Db_Expr("COUNT(*)");

        $select = $connection->select()->from(
            ['rating_vote' => $this->getTable('rating_option_vote')],
            ['entity_pk_value' => 'rating_vote.entity_pk_value', 'sum' => $sumColumn, 'count' => $countColumn]
        )->join(
            ['review' => $this->getTable('review')],
            'rating_vote.review_id=review.review_id',
            []
        )->join(
            ['rating_option' => $this->getTable('rating_option')],
            'rating_option.option_id=rating_vote.option_id',
            []
        )->join(
            ['rating' => $this->getTable('rating')],
            'rating.rating_id=rating_option.rating_id AND rating.is_used_in_summary = 1',
            []
        )->joinLeft(
            ['review_store' => $this->getTable('review_store')],
            'rating_vote.review_id=review_store.review_id',
            ['review_store.store_id']
        );
        if (!$this->_storeManager->isSingleStoreMode()) {
            $select->join(
                ['rating_store' => $this->getTable('rating_store')],
                'rating_store.rating_id = rating_vote.rating_id AND rating_store.store_id = review_store.store_id',
                []
            );
        }
        $select->join(
            ['review_status' => $this->getTable('review_status')],
            'review.status_id = review_status.status_id',
            []
        )->where(
            'review_status.status_code = :status_code'
        )->group(
            'rating_vote.entity_pk_value'
        )->group(
            'review_store.store_id'
        );
        $bind = [':status_code' => self::RATING_STATUS_APPROVED];

        $entityPkValue = $object->getEntityPkValue();
        if ($entityPkValue) {
            $select->where('rating_vote.entity_pk_value = :pk_value');
            $bind[':pk_value'] = $entityPkValue;
        }

        return $connection->fetchAll($select, $bind);
    }
}
