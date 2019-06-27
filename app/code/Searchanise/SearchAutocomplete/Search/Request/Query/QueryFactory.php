<?php

namespace Searchanise\SearchAutocomplete\Search\Request\Query;

/**
 * Factory for search request queries.
 */
class QueryFactory
{
    /**
     * @var array
     */
    private $_factories;

    /**
     * Constructor.
     *
     * @param array $factories Query factories by type.
     */
    public function __construct($factories = [])
    {
        $this->_factories = $factories;
    }

    /**
     * Create a query from it's type and params.
     *
     * @param string $queryType   Query type (must be a valid query type defined into the factories array).
     * @param array  $queryParams Query constructor params.
     *
     * @return QueryInterface
     */
    public function create($queryType, $queryParams)
    {
        if (!isset($this->_factories[$queryType])) {
            throw new \LogicException("No factory found for query of type {$queryType}");
        }

        return $this->_factories[$queryType]->create($queryParams);
    }
}
