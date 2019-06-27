<?php

namespace Searchanise\SearchAutocomplete\Search\Request;

/**
 * Define new usable query types in Searchanise.
 */
interface QueryInterface extends \Magento\Framework\Search\Request\QueryInterface
{
    const DEFAULT_BOOST_VALUE = 1;

    //const TYPE_COMMON = 'commonQuery';
    const TYPE_TERM     = 'termQuery';
    const TYPE_TERMS    = 'termsQuery';
    const TYPE_RANGE    = 'rangeQuery';
    const TYPE_BOOLEAN  = 'boolQuery';
}
