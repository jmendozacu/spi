<?php

namespace BinaryAnvil\Ratings\Preference\Magento\Review\Model;

use Magento\Review\Model\Rating as OriginalClass;

class Rating extends OriginalClass
{
    const IS_USED_IN_SUMMARY = 'is_used_in_summary';
    const LABEL_MIN = 'label_min';
    const LABEL_PERFECT = 'label_perfect';
    const LABEL_MAX = 'label_max';

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

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
     * @param \Magento\Framework\App\State $state
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Review\Model\ResourceModel\Review\Summary $reviewSummary,
        \Magento\Framework\App\State $state,
        $connectionName = null
    )
    {
        $this->_state = $state;
        parent::__construct($context, $logger, $moduleManager, $storeManager, $reviewSummary, $connectionName);
    }


    /**
     * @return bool
     */
    public function isUsedInSummary()
    {
        return $this->getData(self::IS_USED_IN_SUMMARY);
    }

    /**
     * @param bool $isUsedInSummary
     * @return $this
     */
    public function setIsUsedInSummary($isUsedInSummary)
    {
        return $this->setData(self::IS_USED_IN_SUMMARY, $isUsedInSummary);
    }

    /**
     * @return string|null
     */
    public function getLabelMin()
    {
        return $this->getData(self::LABEL_MIN);
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabelMin($label)
    {
        return $this->setData(self::LABEL_MIN, $label);
    }

    /**
     * @return string|null
     */
    public function getLabelPerfect()
    {
        return $this->getData(self::LABEL_PERFECT);
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabelPerfect($label)
    {
        return $this->setData(self::LABEL_PERFECT, $label);
    }

    /**
     * @return string|null
     */
    public function getLabelMax()
    {
        return $this->getData(self::LABEL_MAX);
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabelMax($label)
    {
        return $this->setData(self::LABEL_MAX, $label);
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
