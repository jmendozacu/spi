<?php

namespace Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\Builder;

use Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\BuilderInterface;

/**
 * Build an terms query.
 */
class Terms implements BuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildQuery(\Magento\Framework\Search\Request\QueryInterface $query)
    {
        return ['terms' => [$query->getField() => $query->getValues()]];
    }
}
