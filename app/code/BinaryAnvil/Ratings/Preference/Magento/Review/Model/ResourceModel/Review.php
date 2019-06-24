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
namespace BinaryAnvil\Ratings\Preference\Magento\Review\Model\ResourceModel;

use Magento\Review\Model\ResourceModel\Review as OriginClass;
use Magento\Framework\Model\AbstractModel;
use BinaryAnvil\Ratings\Preference\Magento\Review\Model\Review as ReviewModel;

class Review extends OriginClass
{
    /**
     * Perform actions after object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _afterSave(AbstractModel $object)
    {
        $connection = $this->getConnection();
        /**
         * save detail
         */
        $detail = [
            'title' => $object->getTitle(),
            'detail' => $object->getDetail(),
            'nickname' => $object->getNickname(),
            ReviewModel::IS_RECOMMEND_PRODUCT =>
                $object->isRecommendProduct() == 'on' || $object->isRecommendProduct() == 1 ? 1 : 0
        ];
        $select = $connection->select()->from($this->_reviewDetailTable, 'detail_id')->where('review_id = :review_id');
        $detailId = $connection->fetchOne($select, [':review_id' => $object->getId()]);

        if ($detailId) {
            $condition = ["detail_id = ?" => $detailId];
            $connection->update($this->_reviewDetailTable, $detail, $condition);
        } else {
            $detail['store_id'] = $object->getStoreId();
            $detail['customer_id'] = $object->getCustomerId();
            $detail['review_id'] = $object->getId();
            $connection->insert($this->_reviewDetailTable, $detail);
        }

        /**
         * save stores
         */
        $stores = $object->getStores();
        if (!empty($stores)) {
            $condition = ['review_id = ?' => $object->getId()];
            $connection->delete($this->_reviewStoreTable, $condition);

            $insertedStoreIds = [];
            foreach ($stores as $storeId) {
                if (in_array($storeId, $insertedStoreIds)) {
                    continue;
                }

                $insertedStoreIds[] = $storeId;
                $storeInsert = ['store_id' => $storeId, 'review_id' => $object->getId()];
                $connection->insert($this->_reviewStoreTable, $storeInsert);
            }
        }

        $this->_aggregateRatings($this->_loadVotedRatingIds($object->getId()), $object->getEntityPkValue());

        return $this;
    }
}
