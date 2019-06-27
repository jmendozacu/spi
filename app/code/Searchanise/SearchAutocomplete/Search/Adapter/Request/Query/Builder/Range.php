<?php

namespace Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\Builder;

use Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\BuilderInterface;

/**
 * Build a Searchanise range query.
 */
class Range implements BuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildQuery(\Magento\Framework\Search\Request\QueryInterface $query)
    {
        return ['range' => [$query->getField() => $query->getBounds()]];
    }
}
