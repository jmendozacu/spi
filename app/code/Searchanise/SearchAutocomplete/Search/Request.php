<?php

namespace Searchanise\SearchAutocomplete\Search;

/**
 * Default implementation of Searchanise search request.
 */
class Request extends \Magento\Framework\Search\Request implements RequestInterface
{
    /**
     * @var string
     */
    private $_type;

    /**
     * @var SortOrderInterface
     */
    private $_sortOrders;

    /**
     * @var \Magento\Framework\Search\Request\QueryInterface
     */
    private $_filter;

    public function __construct(
        $name,
        $indexName,
        $type,
        \Magento\Framework\Search\Request\QueryInterface $query,
        \Magento\Framework\Search\Request\QueryInterface $filter = null,
        array $sortOrders = null,
        $from = null,
        $size = null,
        array $dimensions = [],
        array $buckets = []
    ) {
        parent::__construct($name, $indexName, $query, $from, $size, $dimensions, $buckets);

        $this->_type = $type;
        $this->_filter = $filter;
        $this->_sortOrders = $sortOrders;
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilter()
    {
        return $this->_filter;
    }

    /**
     * {@inheritDoc}
     */
    public function getSortOrders()
    {
        return $this->_sortOrders;
    }
}
