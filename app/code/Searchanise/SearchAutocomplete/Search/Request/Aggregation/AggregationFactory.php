<?php

namespace Searchanise\SearchAutocomplete\Search\Request\Aggregation;

/**
 * Factory for search request aggregation buckets.
 */
class AggregationFactory
{
    /**
     * @var array
     */
    private $_factories;

    /**
     * Constructor.
     *
     * @param array $factories Aggregation factories by type.
     */
    public function __construct($factories = [])
    {
        $this->_factories = $factories;
    }

    /**
     * Create a new bucket from it's type and params.
     *
     * @param string $bucketType   Bucket type (must be a valid bucket type defined into the factories array).
     * @param array  $bucketParams Bucket constructor params.
     *
     * @return BucketInterface
     */
    public function create($bucketType, $bucketParams)
    {
        if (!isset($this->_factories[$bucketType])) {
            throw new \LogicException("No factory found for query of type {$bucketType}");
        }

        return $this->_factories[$bucketType]->create($bucketParams);
    }
}
