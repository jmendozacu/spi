<?php

namespace Searchanise\SearchAutocomplete\Search\Request\Aggregation\Bucket;

use Searchanise\SearchAutocomplete\Search\Request\BucketInterface;
use Searchanise\SearchAutocomplete\Search\Request\QueryInterface;

/**
 * Abstract bucket implementation.
 */
abstract class AbstractBucket implements BucketInterface
{
    /**
     * @var string
     */
    private $_name;

    /**
     * @var string
     */
    private $_field;

    /**
     * @var Metric[]
     */
    private $_metrics;

    /**
     * @var QueryInterface|null
     */
    private $_filter;

    public function __construct(
        $name,
        $field,
        array $metrics,
        QueryInterface $filter = null
    ) {
        $this->_name         = $name;
        $this->_field        = $field;
        $this->_metrics      = $metrics;
        $this->_filter       = $filter;
    }

    /**
     * {@inheritDoc}
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * {@inheritDoc}
     */
    public function getMetrics()
    {
        return $this->metrics;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilter()
    {
        return $this->filter;
    }
}
