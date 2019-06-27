<?php

namespace Searchanise\SearchAutocomplete\Search;

/**
 * Searchanise search requests interface.
 */
interface RequestInterface extends \Magento\Framework\Search\RequestInterface
{
    const TEXT_FIND          = 'TEXT_FIND';
    const TEXT_ADVANCED_FIND = 'TEXT_ADVANCED_FIND';

    /**
     * Searched doucument type.
     *
     * @return string
     */
    public function getType();

    /**
     * Hits filter (does not apply to aggregations).
     *
     * @return QueryInterface
     */
    public function getFilter();

    /**
     * Request sort order.
     *
     * @return SortOrderInterface[]
     */
    public function getSortOrders();
}
