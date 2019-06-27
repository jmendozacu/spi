<?php

namespace Searchanise\SearchAutocomplete\Search\Adapter\Request;

use Searchanise\SearchAutocomplete\Search\RequestInterface;
use Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\Builder as QueryBuilder;
use Searchanise\SearchAutocomplete\Search\Adapter\Request\SortOrder\Builder as SortOrderBuilder;
use Searchanise\SearchAutocomplete\Search\Adapter\Request\Aggregation\Builder as AggregationBuilder;
use Searchanise\SearchAutocomplete\Search\Request\SortOrderInterface;

use \Magento\Framework\Search\Request\QueryInterface as RequestQueryInterface;
use \Magento\Framework\Search\Request\FilterInterface;

/**
 * Map a search request into a Searchanise Search query.
 */
class Mapper
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
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Searchanise\SearchAutocomplete\Helper\ApiSe
     */
    private $apiSeHelper;

    public function __construct(
        QueryBuilder $queryBuilder,
        SortOrderBuilder $sortOrderBuilder,
        AggregationBuilder $aggregationBuilder,
        \Magento\Framework\App\Request\Http $request,
        \Searchanise\SearchAutocomplete\Helper\ApiSe $apiSeHelper
    ) {
        $this->_queryBuilder       = $queryBuilder;
        $this->_sortOrderBuilder   = $sortOrderBuilder;
        $this->_aggregationBuilder = $aggregationBuilder;
        $this->request = $request;
        $this->apiSeHelper = $apiSeHelper;
    }

    /**
     * Transform the search request into an Searchanise request.
     *
     * @param RequestInterface $request Search Request.
     *
     * @return array
     */
    public function buildSearchRequest(\Magento\Framework\Search\RequestInterface $request)
    {
        $searchRequest = [];

        $searchRequest['restrictBy']['status'] = '1';
        $searchRequest['union']['price']['min'] = \Searchanise\SearchAutocomplete\Helper\ApiSe::getLabelForPricesUsergroup();

        if (!$this->apiSeHelper->isShowOutOfStockProducts()) {
            $searchRequest['restrictBy']['is_in_stock'] = '1';
        }

        $query = $this->_getRootQuery($request);
        if (!empty($query)) {
            $this->_addFilter($query, $searchRequest);
        }

        // TODO: Check if it realy needed
        $sort = $this->getSortOrder($request);
        if (!empty($sort)) {
            $searchRequest['sortBy'] = $sort['order'];
            $searchRequest['sortOrder'] = $sort['direction'];
        }

        if ($request->getName() == 'advanced_search_container') {
            if (!empty($searchRequest['search'])) {
                unset($searchRequest['search']);
            }
        } elseif ($request->getName() == 'quick_search_container') {
            if (!empty($searchRequest['search'])) {
                $searchRequest['q'] = strtolower(trim($searchRequest['search']));
                unset($searchRequest['search']);
            }

            // Do not use queryBy in quick search
            if (!empty($searchRequest['queryBy'])) {
                unset($searchRequest['queryBy']);
            }
        }

        if ($request->getIndex() == 'catalogsearch_fulltext') {
            $searchRequest['facets']           = 'true';
            $searchRequest['suggestions']      = 'true';
            $searchRequest['query_correction'] = 'false';
        } else {
            $searchRequest['facets']           = 'true';
            $searchRequest['suggestions']      = 'false';
            $searchRequest['query_correction'] = 'false';
        }

        $searchRequest['startIndex'] = $request->getFrom();
        $searchRequest['maxResults'] = $request->getSize();

        return $searchRequest;
    }

    /**
     * Adds query parameters to Searchanise request
     *
     * @param array $query         Query
     * @param array $searchRequest Searchanise request
     */
    private function _addQuery(array $query, array &$searchRequest)
    {
        if (!empty($query)) {
            foreach ($query as $type => $data) {
                if (!empty($data['query']) && is_array($data['query'])) {
                    $this->_addQuery($data['query'], $searchRequest);
                } else {
                    // TODO: Adds additional types
                    if ($type == 'match') {
                        foreach ($data as $field => $q) {
                            if (!empty($q['query'])) {
                                $this->_addQueryBy($searchRequest, $field, $q['query']);
                            }
                        }
                    }

                    if ($type == 'filtered') {
                        $this->_addFilter($data, $searchRequest);
                    }
                }

                if (!empty($data['filter']) && is_array($data['filter'])) {
                    $this->_addFilter($data['filter'], $searchRequest);
                }
            }
        }

        return true;
    }

    /**
     * Add filters to searchanise request
     * 
     * @param array $filter
     * @param array $searchRequest
     */
    private function _addFilter(array $filter, array &$searchRequest)
    {
        if (empty($filter)) {
            return;
        }

        if (!isset($searchRequest['restrictBy'])) {
            $searchRequest['restrictBy'] = [];
        }

        foreach ($filter as $type => $condition) {
            if ($type == 'terms' || $type == 'term') {
                foreach ($condition as $field => $cond) {
                    $searchRequest['restrictBy'][$field] = is_array($cond) ? implode('|', $cond) : $cond;
                }
            } elseif ($type == 'match') {
                $searchRequest['queryBy'] = isset($searchRequest['queryBy']) ? $searchRequest['queryBy'] : [];

                foreach ($condition as $field => $matchCondition) {
                    $this->_addQueryBy($searchRequest, $field, $matchCondition['query']);
                }
            } elseif ($type == 'boolean') {
                if (!empty($condition['must']) && is_array($condition['must'])) {
                    foreach ($condition['must'] as $mustCondition) {
                        $this->_addFilter($mustCondition, $searchRequest);
                    }
                }

                if (!empty($condition['should']) && is_array($condition['should'])) {
                    foreach ($condition['should'] as $shouldCondition) {
                        $this->_addQuery($shouldCondition, $searchRequest);
                    }
                }
            } elseif ($type == 'range') {
                foreach ($condition as $field => $rangeCondition) {
                    $searchRequest['restrictBy'][$field] =
                    (!empty($rangeCondition['gte']) ? $rangeCondition['gte'] : '')
                    . ','
                    . (!empty($rangeCondition['lte']) ? $rangeCondition['lte'] : '');
                }
            } elseif ($type == 'filtered') {
                $this->_addFilter($condition, $searchRequest);
            }
        }
    }

    /**
     * Adds query to search request
     *
     * @param array $searchRequest  Search request data
     * @param string $field         Field for search
     * @param string $query         Search value
     * @return boolean
     */
    private function _addQueryBy(&$searchRequest, $field, $query)
    {
        if (empty($query) || in_array($field, array('tax_class_id', 'status'))) {
            return false;
        }

        $field = $field == 'name' ? 'title' : $field;
        $field = $field == 'sku' ? 'product_code' : $field;
        $field = $field == 'description' ? 'full_description' : $field;
        $field = $field == 'short_description' ? 'description' : $field;

        if ($field == '*') {
            $searchRequest['search'] = $query;
        } else {
            $searchRequest['queryBy'][$field] = $query;
        }

        return true;
    }

    /**
     * Extract and build the root query of the search request.
     *
     * @param RequestInterface $request Search request.
     *
     * @return array
     */
    private function _getRootQuery(\Magento\Framework\Search\RequestInterface $request)
    {
        $query = null;

        if ($request->getQuery()) {
            $query = $this->_queryBuilder->buildQuery($request->getQuery());
        }

        return $query;
    }

    private function getSortOrder(\Magento\Framework\Search\RequestInterface $request)
    {
        $mapOrders = [
            'name' => 'title'
        ];

        $direction = $this->request->getParam('product_list_dir');
        $order = $this->request->getParam('product_list_order');

        if (empty($order)) {
            $order = 'relevance';
        }

        $sortOrder = [
            'order'         => !empty($mapOrders[$order]) ? $mapOrders[$order] : $order,
            'direction'     => !empty($direction) ? $direction : 'desc',
        ];

        return $sortOrder;
    }

    /**
     * Extract and build sort orders of the search request.
     *
     * @param RequestInterface $request Search request.
     *
     * @return array
     * @todo NotUsed, removed in future
     */
    private function _getSortOrders(\Magento\Framework\Search\RequestInterface $request)
    {
        if ($request->getSortOrders()) {
            $sortOrders = $this->_sortOrderBuilder->buildSortOrders($request->getSortOrders());
        }

        return $sortOrders;
    }

    /**
     * Extract and build aggregations of the search request.
     *
     * @param RequestInterface $request Search request.
     *
     * @return array
     * @todo NotUsed, removed in future
     */
    private function _getAggregations(\Magento\Framework\Search\RequestInterface $request)
    {
        $aggregations = [];

        if ($request->getAggregation()) {
            $aggregations = $this->_aggregationBuilder->buildAggregations($request->getAggregation());
        }

        return $aggregations;
    }
}
