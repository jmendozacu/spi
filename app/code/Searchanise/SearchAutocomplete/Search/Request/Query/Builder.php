<?php

namespace Searchanise\SearchAutocomplete\Search\Request\Query;

use Searchanise\SearchAutocomplete\Search\Request\QueryInterface;
use Searchanise\SearchAutocomplete\Search\Request\Query\QueryFactory;
use Searchanise\SearchAutocomplete\Search\Request\Query\Filter\Builder as FilterQueryBuilder;
use Searchanise\SearchAutocomplete\Search\Request\Query\Fulltext\Builder as FulltextQueryBuilder;

/**
 * Builder for query part of the search request.
 */
class Builder
{
    /**
     * @var QueryFactory
     */
    private $_queryFactory;

    /**
     * @var FulltextQueryBuilder
     */
    private $_fulltextQueryBuilder;

    /**
     * @var FilterQueryBuilder
     */
    private $_filterQueryBuilder;

    public function __construct(
        QueryFactory $queryFactory,
        FulltextQueryBuilder $fulltextQueryBuilder,
        FilterQueryBuilder $filterQuerybuilder
    ) {
        $this->_queryFactory         = $queryFactory;
        $this->_fulltextQueryBuilder = $fulltextQueryBuilder;
        $this->_filterQueryBuilder   = $filterQuerybuilder;
    }

    /**
     * Create a filtered query with an optional fulltext query part.
     *
     * @param string|null $queryText    Fulltext query.
     * @param array       $filters      Filter part of the query.
     * @param string      $spellingType For fulltext query
     *
     * @return QueryInterface
     */
    public function createQuery($queryText, array $filters)
    {
        $queryParams = [];

        if (!empty($filters)) {
            $queryParams = ['filter' => $this->createFilters($filters)];
        }

        if ($queryText) {
            $queryParams['query'] = $this->_fulltextQueryBuilder->create($queryText);
        }

        return $this->_queryFactory->create(QueryInterface::TYPE_FILTER, $queryParams);
    }

    /**
     * Create a query from filters passed as arguments.
     *
     * @param array $filters Filters used to build the query.
     *
     * @return QueryInterface
     */
    public function createFilters(array $filters)
    {
        return $this->_filterQueryBuilder->create($filters);
    }
}
