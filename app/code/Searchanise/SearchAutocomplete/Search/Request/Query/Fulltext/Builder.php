<?php

namespace Searchanise\SearchAutocomplete\Search\Request\Query\Fulltext;

use Searchanise\SearchAutocomplete\Search\Request\Query\QueryFactory;
use Searchanise\SearchAutocomplete\Search\Request\QueryInterface;

/**
 * Prepare a fulltext search query.
 */
class Builder
{
    // TODO: Moved from here
    const DEFAULT_SEARCH_FIELD = 'q';

    /**
     * @var QueryFactory
     */
    private $_queryFactory;

    /**
     * Constructor.
     *
     * @param QueryFactory $queryFactory Query factory (used to build subqueries.
     */
    public function __construct(QueryFactory $queryFactory)
    {
        $this->_queryFactory = $queryFactory;
    }

    /**
     * Create the fulltext search query.
     *
     * @param string $queryText The text query.
     * @param float  $boost     Boost of the created query.
     *
     * @return QueryInterface
     */
    public function create($queryText, $boost = 1)
    {
        $query = null;

        if (is_array($queryText)) {
            $queries = [];

            foreach ($queryText as $currentQueryText) {
                $queries[] = $this->create($currentQueryText);
            }

            $query = $this->_queryFactory->create(QueryInterface::TYPE_BOOLEAN, ['should' => $queries, 'boost' => $boost]);
        } else {
            $queryParams = [
                'field'     => self::DEFAULT_SEARCH_FIELD,
                'queryText' => $queryText,
            ];

            $query = $this->_queryFactory->create(QueryInterface::TYPE_MATCH, $queryParams);
        }

        return $query;
    }
}
