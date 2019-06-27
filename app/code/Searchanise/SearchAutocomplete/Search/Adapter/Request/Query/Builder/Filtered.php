<?php

namespace Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\Builder;

use Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\BuilderInterface;
use Searchanise\SearchAutocomplete\Search\Request\Query\Term as TermQuery;
use Searchanise\SearchAutocomplete\Search\Request\Query\Range as RangeQuery;
use Searchanise\SearchAutocomplete\Search\Request\Query\Match as MatchQuery;

/**
 * Build an filtered query.
 */
class Filtered extends AbstractBuilder implements BuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildQuery(\Magento\Framework\Search\Request\QueryInterface $query)
    {
        $searchQuery = [];
        $ref = $query->getReference();

        if ($ref instanceof \Magento\Framework\Search\Request\Filter\Term) {
            $termQuery = new TermQuery($ref->getValue(), $ref->getField(), $ref->getName());
            $searchQuery = $this->parentBuilder->buildQuery($termQuery);
        }

        if ($ref instanceof \Magento\Framework\Search\Request\Filter\Range) {
            $rangeQuery = new RangeQuery($ref->getField(), [
                'gte' => $ref->getFrom(),
                'lte' => $ref->getTo()
            ], $ref->getName());
            $searchQuery = $this->parentBuilder->buildQuery($rangeQuery);
        }

        if ($ref instanceof \Magento\Framework\Search\Request\Filter\Wildcard) {
            $matchQuery = new MatchQuery($ref->getValue(), $ref->getField(), $ref->getName());
            $searchQuery = $this->parentBuilder->buildQuery($matchQuery);
        }

        $searchQuery['boost'] = $query->getBoost();

        return ['filtered' => $searchQuery];
    }
}
