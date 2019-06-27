<?php

namespace Searchanise\SearchAutocomplete\Search\Adapter\Request\SortOrder;

use Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\Builder as QueryBuilder;
use Searchanise\SearchAutocomplete\Search\Request\SortOrderInterface;

/**
 * Build sort orders from search request specification interface.
 */
class Builder
{
    /**
     * @var QueryBuilder
     */
    private $_queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->_queryBuilder = $queryBuilder;
    }

    /**
     * Build sort orders.
     *
     * @param SortOrderInterface[] $sortOrders Sort orders specification.
     *
     * @return array
     */
    public function buildSortOrders(array $sortOrders = [])
    {
        return array_map([$this, '_buildSortOrder'], $sortOrders);
    }

    /**
     * Build a sort order condition from a SortOrderInterface specification.
     *
     * @param  SortOrderInterface $sortOrder Request sort order specification object.
     * @return array
     */
    private function _buildSortOrder(SortOrderInterface $sortOrder)
    {
        $sortField = $sortOrder->getField();

        $sortOrderConfig = [
            'order'         => $sortOrder->getDirection(),
            'missing'       => $sortOrder->getDirection() == SortOrderInterface::SORT_ASC ? '_last' : '_first',
            //'unmapped_type' => FieldInterface::FIELD_TYPE_STRING,
            'unmapped_type' => 'string' //FieldInterface::FIELD_TYPE_STRING,
        ];

        return [$sortField => $sortOrderConfig];
    }
}
