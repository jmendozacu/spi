<?php

namespace Searchanise\SearchAutocomplete\Search\Request;

/**
 * Extension of Magento default bucket interface
 */
interface BucketInterface extends \Magento\Framework\Search\Request\BucketInterface
{
    const SORT_ORDER_COUNT     = '_count';
    const SORT_ORDER_TERM      = '_term';
    const SORT_ORDER_RELEVANCE = "_score";
    const SORT_ORDER_MANUAL    = "_manual";

    /**
     * Optional filter for filtered aggregations.
     *
     * @return QueryInterface|null
     */
    public function getFilter();
}
