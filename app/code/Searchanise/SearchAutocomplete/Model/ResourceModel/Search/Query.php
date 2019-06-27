<?php

namespace Searchanise\SearchAutocomplete\Model\ResourceModel\Search;

/**
 * Custom search request resource model.
 */
class Query extends \Magento\Search\Model\ResourceModel\Query
{
    /**
     * Save query with number of results and is spellchecked.
     *
     * @param  \Magento\Search\Model\Query $query Search query object.
     * @return void
     */
    public function saveSearchResults(\Magento\Search\Model\Query $query)
    {
    }
}
