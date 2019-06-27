<?php

namespace Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\Builder;

use Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\BuilderInterface;

/**
 * Build a term query.
 */
class Term implements BuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildQuery(\Magento\Framework\Search\Request\QueryInterface $query)
    {
        return ['term' => [$query->getField() => $query->getValue()]];
    }
}
