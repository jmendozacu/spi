<?php

namespace Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\Builder;

use Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\Builder;

/**
 * Complex builder are able to used the global builder to build subqueries.
 */
abstract class AbstractBuilder
{
    /**
     * @var Builder
     */
    protected $parentBuilder;

    /**
     * Constructor.
     *
     * @param Builder $builder Parent builder used to build subqueries.
     */
    public function __construct(Builder $builder)
    {
        $this->parentBuilder = $builder;
    }
}
