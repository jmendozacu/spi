<?php

namespace Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\Builder;

use Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\BuilderInterface;

/**
 * Build an match query.
 */
class Match implements BuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildQuery(\Magento\Framework\Search\Request\QueryInterface $query)
    {
        $searchQueryParams = [
            'query'                => method_exists($query, 'getQueryText')
                ? $query->getQueryText()
                : $query->getValue(),
            'boost'                => $query->getBoost(),
        ];

        $queryMatch = [];

        if (method_exists($query, 'getMatches')) {
            $matches = $query->getMatches();

            if (!empty($matches)) {
                foreach ($matches as $match) {
                    $queryMatch[$match['field']] = $searchQueryParams;
                }
            }
        }

        if (method_exists($query, 'getField')) {
            $queryMatch[$query->getField()] = $searchQueryParams;
        }

        return ['match' => $queryMatch];
    }
}
