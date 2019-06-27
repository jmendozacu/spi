<?php

namespace Searchanise\SearchAutocomplete\Search\Request\Aggregation;

use Searchanise\SearchAutocomplete\Search\Request\Query\Builder as QueryBuilder;

/**
 * Build aggregation from the mapping.
 */
class Builder
{
    /**
     * @var AggregationFactory
     */
    private $_aggregationFactory;

    /**
     * @var QueryBuilder
     */
    private $_queryBuilder;

    private $_mapFields = [];

    public function __construct(
        AggregationFactory $aggregationFactory,
        QueryBuilder $queryBuilder
    ) {
        $this->_aggregationFactory = $aggregationFactory;
        $this->_queryBuilder       = $queryBuilder;
    }

    /**
     * Build the list of buckets from the mapping.
     *
     * @param array $aggregations Facet definitions.
     * @param array $filters      Facet filters to be added to buckets.
     *
     * @return BucketInterface[]
     */
    public function buildAggregations(
        array $aggregations,
        array $filters
    ) {
        $buckets = [];

        foreach ($aggregations as $fieldName => $aggregationParams) {
            $bucketType = $aggregationParams['type'];

            try {
                $bucketParams = $this->_getBucketParams($fieldName, $aggregationParams, $filters);

                if (isset($bucketParams['filter'])) {
                    $bucketParams['filter'] = $this->_createFilter($bucketParams['filter']);
                }
            } catch (\Exception $e) {
                $bucketParams = $aggregationParams['config'];
            }

            $buckets[] = $this->_aggregationFactory->create($bucketType, $bucketParams);
        }

        return $buckets;
    }

    /**
     * Create a QueryInterface for a filter using the query builder.
     *
     * @param array $filters Filters definition.
     *
     * @return QueryInterface
     */
    private function _createFilter(array $filters)
    {
        return $this->_queryBuilder->createFilters($filters);
    }

    /**
     * Preprocess aggregations params before they are used into the aggregation factory.
     *
     * @param string $field             Bucket field.
     * @param array  $aggregationParams Aggregation params.
     * @param array  $filters           Filter applied to the search request.
     *
     * @return array
     */
    private function _getBucketParams($field, array $aggregationParams, array $filters)
    {
        $bucketField = $this->_mapBucketField($field);

        $bucketParams = [
            'field'   => $bucketField,
            'name'    => $field,
            'metrics' => [],
            'filter' => array_diff_key($filters, [$field => true]),
        ];

        $bucketParams += $aggregationParams['config'];

        if (empty($bucketParams['filter'])) {
            unset($bucketParams['filter']);
        }

        return $bucketParams;
    }

    private function _mapBucketField($field)
    {
        if (isset($this->_mapFields[$field])) {
            $field = $this->_mapFields[$field];
        }

        return $field;
    }
}
