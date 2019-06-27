<?php

namespace Searchanise\SearchAutocomplete\Search\Adapter\Request\Aggregation;

use Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\Builder as QueryBuilder;
use Searchanise\SearchAutocomplete\Search\Request\BucketInterface;

class Builder
{
    /**
     * @var QueryBuilder
     */
    private $_queryBuilder;
    /**
     * @var ObjectManagerInterface
     */
    private $_objectManager;

    /**
     * @var array
     */
    private $_bucketBuilderClasses = [
        BucketInterface::TYPE_TERM => 'Searchanise\SearchAutocomplete\Search\Adapter\Request\Aggregation\Builder\Term',
    ];

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, QueryBuilder $queryBuilder)
    {
        $this->_objectManager = $objectManager;
        $this->_queryBuilder  = $queryBuilder;
    }

    public function buildAggregations(array $buckets = [])
    {
        $aggregations = [];

        foreach ($buckets as $bucket) {
        }

        return $aggregations;
    }

    /**
     * Retrieve the builder used to convert a bucket into aggregation.
     *
     * @param string $bucketType Bucket type to be built.
     *
     * @return object
     */
    private function _getBuilder($bucketType)
    {
        if (isset($this->bucketBuilderClasses[$bucketType])) {
            $builderClass = $this->bucketBuilderClasses[$bucketType];
            $builder = $this->objectManager->get($builderClass, ['builder' => $this]);
        }

        return $builder;
    }
}
