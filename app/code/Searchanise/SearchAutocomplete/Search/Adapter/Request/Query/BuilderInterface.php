<?php

namespace Searchanise\SearchAutocomplete\Search\Adapter\Request\Query;

/**
 * Build Searchanise queries from search request QueryInterface queries.
 */
interface BuilderInterface
{
    /**
     * Build the Searchanise query from a Query
     *
     * @param QueryInterface $query Query to be built.
     *
     * @return array
     */
    public function buildQuery(\Magento\Framework\Search\Request\QueryInterface $query);
}
