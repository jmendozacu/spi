<?php

namespace Searchanise\SearchAutocomplete\Search\Request\SortOrder;

use Searchanise\SearchAutocomplete\Search\Request\Query\Filter\Builder as QueryBuilder;
use Searchanise\SearchAutocomplete\Search\Request\SortOrderInterface;

/**
 * Allow to build a sort order from arrays.
 */
class Builder
{
    /**
     * @var StandardFactory
     */
    private $_standardOrderFactory;

    /**
     * @var QueryBuilder
     */
    private $_queryBuilder;

    public function __construct(
        StandardFactory $standardOrderFactory,
        QueryBuilder $queryBuilder
    ) {
        $this->_standardOrderFactory = $standardOrderFactory;
        $this->_queryBuilder = $queryBuilder;
    }

    /**
     * Build sort orders from array of sort orders definition.
     *
     * @param array $orders Sort orders definitions.
     *
     * @return SortOrderInterface[]
     */
    public function buildSordOrders(array $orders)
    {
        $sortOrders = [];

        if (empty($orders)) {
            $orders = $this->_addDefaultSortOrders($orders);
        }

        foreach ($orders as $fieldName => $sortOrderParams) {
            $sortOrderParams['field'] = $fieldName;
            $sortOrders[] = $this->_standardOrderFactory->create($sortOrderParams);
        }

        return $sortOrders;
    }

    /**
     * Append default sort to all queries to get fully predictable search results.
     *
     * Order by _score first and then by the id field.
     *
     * @param array $orders Original orders.
     *
     * @return array
     */
    private function _addDefaultSortOrders($orders)
    {
        $defaultOrders = [
            SortOrderInterface::DEFAULT_SORT_NAME => SortOrderInterface::SORT_DESC,
        ];

        if (count($orders) > 0) {
            $firstOrder = current($orders);

            if ($firstOrder['direction'] == SortOrderInterface::SORT_DESC) {
                $defaultOrders[SortOrderInterface::DEFAULT_SORT_NAME] = SortOrderInterface::SORT_ASC;
            }
        }

        foreach ($defaultOrders as $currentOrder => $direction) {
            if (!in_array($currentOrder, array_keys($orders))) {
                $orders[$currentOrder] = ['direction' => $direction];
            }
        }

        return $orders;
    }
}
