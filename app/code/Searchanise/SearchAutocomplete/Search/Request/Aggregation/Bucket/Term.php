<?php

namespace Searchanise\SearchAutocomplete\Search\Request\Aggregation\Bucket;

use Searchanise\SearchAutocomplete\Search\Request\BucketInterface;
use Searchanise\SearchAutocomplete\Search\Request\QueryInterface;

/**
 * Term Bucket implementation.
 */
class Term extends AbstractBucket
{
    /**
     * @var integer
     */
    private $_size;

    /**
     * @var string
     */
    private $_sortOrder;

    public function __construct(
        $name,
        $field,
        array $metrics,
        QueryInterface $filter = null,
        $size = 0,
        $sortOrder = BucketInterface::SORT_ORDER_MANUAL
    ) {
        parent::__construct($name, $field, $metrics, $filter);

        $this->_size = $size;
        $this->_sortOrder = $sortOrder;
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return BucketInterface::TYPE_TERM;
    }

    /**
     * Bucket size.
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * Bucket sort order.
     *
     * @return string
     */
    public function getSortOrder()
    {
        return $this->_sortOrder;
    }
}
