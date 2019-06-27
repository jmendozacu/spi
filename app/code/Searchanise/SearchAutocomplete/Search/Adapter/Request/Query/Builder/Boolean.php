<?php

namespace Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\Builder;

use Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\BuilderInterface;

/**
 * Build an bool query.
 */
class Boolean extends AbstractBuilder implements BuilderInterface
{
    const QUERY_CONDITION_MUST   = 'must';
    const QUERY_CONDITION_NOT    = 'must_not';
    const QUERY_CONDITION_SHOULD = 'should';

    /**
     * {@inheritDoc}
     */
    public function buildQuery(\Magento\Framework\Search\Request\QueryInterface $query)
    {
        $searchQuery = [];

        $clauses = [
            self::QUERY_CONDITION_MUST,
            self::QUERY_CONDITION_NOT,
            self::QUERY_CONDITION_SHOULD,
        ];

        foreach ($clauses as $clause) {
            $queries = array_map(
                [$this->parentBuilder, 'buildQuery'],
                $this->_getQueryClause($query, $clause)
            );
            $searchQuery[$clause] = array_filter($queries);
        }

        $searchQuery['boost'] = $query->getBoost();

        return ['boolean' => $searchQuery];
    }

    /**
     * Return the list of queries associated to a clause.
     *
     * @param QueryInterface $query  Bool query.
     * @param string         $clause Current clause (must, should, must_not).
     *
     * @return QueryInterface[]
     */
    private function _getQueryClause($query, $clause)
    {
        $queries = $query->getMust();

        if ($clause == self::QUERY_CONDITION_NOT) {
            $queries = $query->getMustNot();
        } elseif ($clause == self::QUERY_CONDITION_SHOULD) {
            $queries = $query->getShould();
        }

        return $queries;
    }
}
