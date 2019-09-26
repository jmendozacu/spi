<?php

/** @codingStandardsIgnoreFile */

namespace BinaryAnvil\Ratings\Preference\Magento\Review\Model\ResourceModel;

use Magento\Review\Model\ResourceModel\Rating as OriginalClass;

class Rating extends OriginalClass
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $_state;

    /**
     * Rating constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Review\Model\ResourceModel\Review\Summary $reviewSummary
     * @param null $connectionName
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Review\Model\ResourceModel\Review\Summary $reviewSummary,
        $connectionName = null,
        \Magento\Framework\App\State $state
    )
    {
        $this->_state = $state;
        parent::__construct($context, $logger, $moduleManager, $storeManager, $reviewSummary, $connectionName);
    }


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

    /**
     * Review summary
     *
     * @param \Magento\Review\Model\Rating $object
     * @param boolean $onlyForCurrentStore
     * @return array
     */
    public function getReviewSummary($object, $onlyForCurrentStore = true)
    {
        $connection = $this->getConnection();
        $sumColumn = new \Zend_Db_Expr("SUM(rating_vote.{$connection->quoteIdentifier('percent')})");
        $countColumn = new \Zend_Db_Expr('COUNT(*)');
        $select = $connection->select()->from(
            ['rating_vote' => $this->getTable('rating_option_vote')],
            ['sum' => $sumColumn, 'count' => $countColumn]
        )->joinLeft(
            ['review_store' => $this->getTable('review_store')],
            'rating_vote.review_id = review_store.review_id',
            ['review_store.store_id']
        );
        if (!$this->_storeManager->isSingleStoreMode()) {
            $select->join(
                ['rating_store' => $this->getTable('rating_store')],
                'rating_store.rating_id = rating_vote.rating_id AND rating_store.store_id = review_store.store_id',
                []
            );
        }
        $select->where(
            'rating_vote.review_id = :review_id'
        )->group(
            'rating_vote.review_id'
        )->group(
            'review_store.store_id'
        );
        $data = $connection->fetchAll($select, [':review_id' => $object->getReviewId()]);

        // fix summary rating review
        if ($this->_state->getAreaCode() == "adminhtml") {
            $currentStore = false;
        } else {
            $currentStore = $this->_storeManager->getStore()->setId();
        }
        if ($onlyForCurrentStore) {
            foreach ($data as $row) {
                if ($row['store_id'] == $currentStore) {
                    $object->addData($row);
                }
            }
            return $object;
        }
        // # fix summary rating review

        $result = [];
        $stores = $this->_storeManager->getStore()->getResourceCollection()->load();
        foreach ($data as $row) {
            $clone = clone $object;
            $clone->addData($row);
            $result[$clone->getStoreId()] = $clone;
        }
        $usedStoresId = array_keys($result);
        foreach ($stores as $store) {
            if (!in_array($store->getId(), $usedStoresId)) {
                $clone = clone $object;
                $clone->setCount(0);
                $clone->setSum(0);
                $clone->setStoreId($store->getId());
                $result[$store->getId()] = $clone;
            }
        }
        return array_values($result);
    }
}
