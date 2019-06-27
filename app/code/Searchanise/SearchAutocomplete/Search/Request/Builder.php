<?php

namespace Searchanise\SearchAutocomplete\Search\Request;

use Searchanise\SearchAutocomplete\Search\RequestFactory;
use Searchanise\SearchAutocomplete\Search\Request\Query\Builder as QueryBuilder;
use Searchanise\SearchAutocomplete\Search\Request\SortOrder\Builder as SortOrderBuilder;
use Searchanise\SearchAutocomplete\Search\Request\Aggregation\Builder as AggregationBuilder;

/**
 * Searchanise search requests builder.
 */
class Builder
{
    /**
     * @var QueryBuilder
     */
    private $_queryBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $_sortOrderBuilder;

    /**
     * @var AggregationBuilder
     */
    private $_aggregationBuilder;

    /**
     * @var RequestFactory
     */
    private $_requestFactory;

    /**
     * @var DimensionFactory
     */
    private $_dimensionFactory;

    private $_searchaniseTypes = [
        'quick_search_container' => \Searchanise\SearchAutocomplete\Search\RequestInterface::TEXT_FIND,
        'catalog_view_container' => \Searchanise\SearchAutocomplete\Search\RequestInterface::TEXT_ADVANCED_FIND,
    ];

    public function __construct(
        RequestFactory $requestFactory,
        \Magento\Framework\Search\Request\DimensionFactory $dimensionFactory,
        QueryBuilder $queryBuilder,
        SortOrderBuilder $sortOrderBuilder,
        AggregationBuilder $aggregationBuilder
    ) {
        $this->_requestFactory           = $requestFactory;
        $this->_dimensionFactory         = $dimensionFactory;
        $this->_queryBuilder             = $queryBuilder;
        $this->_sortOrderBuilder         = $sortOrderBuilder;
        $this->_aggregationBuilder       = $aggregationBuilder;
    }

    /**
     * Create a new search request.
     *
     * @param integer          $storeId       Search request store id.
     * @param string           $containerName Search request name.
     * @param integer          $from          Search request pagination from clause.
     * @param integer          $size          Search request pagination size.
     * @param string           $queryText     Search request fulltext query.
     * @param array            $sortOrders    Search request sort orders.
     * @param array            $filters       Search request filters.
     * @param QueryInterface[] $queryFilters  Search request filters prebuilt as QueryInterface.
     * @param array            $facets        Search request facets.
     *
     * @return RequestInterface
     */
    public function create(
        $storeId,
        $containerName,
        $from,
        $size,
        $queryText = null,
        $sortOrders = [],
        $filters = [],
        $queryFilters = [],
        $facets = []
    ) {
        $facetFilters  = array_intersect_key($filters, $facets);
        $queryFilters  = array_merge($queryFilters, array_diff_key($filters, $facetFilters));

        $requestParams = [
            'name'         => $containerName,
            'indexName'    => 'catalogsearch_fulltext',
            'type'         => isset($this->_searchaniseTypes[$containerName]) ? $this->_searchaniseTypes[$containerName] : \Searchanise\SearchAutocomplete\Search\RequestInterface::TEXT_FIND,
            'from'         => $from,
            'size'         => $size,
            'dimensions'   => $this->_buildDimensions($storeId),
            'query'        => $this->_queryBuilder->createQuery($queryText, $queryFilters),
            'sortOrders'   => $this->_sortOrderBuilder->buildSordOrders($sortOrders),
            'buckets'      => $this->_aggregationBuilder->buildAggregations($facets, $facetFilters),
        ];

        if (!empty($facetFilters)) {
            $requestParams['filter'] = $this->_queryBuilder->createFilters($facetFilters);
        }

        $request = $this->_requestFactory->create($requestParams);

        return $request;
    }

    /**
     * Build a dimenstion object from
     * It is quite useless since we have a per store index but required by the RequestInterface specification.
     *
     * @param  integer $storeId Store id.
     * @return Dimension[]
     */
    private function _buildDimensions($storeId)
    {
        $dimensions = ['scope' => $this->_dimensionFactory->create(['name' => 'scope', 'value' => $storeId])];

        return $dimensions;
    }
}
