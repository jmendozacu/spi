<?php

namespace Searchanise\SearchAutocomplete\Search\Request\Query;

use Searchanise\SearchAutocomplete\Search\Request\QueryInterface;

/**
 * Filtered query definition.
 */
class Filtered implements QueryInterface
{
    /**
     * @var string
     */
    private $_name;

    /**
     * @var integer
     */
    private $_boost;

    /**
     * @var QueryInterface
     */
    private $_filter;

    /**
     * @var QueryInterface
     */
    private $_query;

    public function __construct(
        \Magento\Framework\Search\Request\QueryInterface $query = null,
        \Magento\Framework\Search\Request\QueryInterface $filter = null,
        $name = null,
        $boost = QueryInterface::DEFAULT_BOOST_VALUE
    ) {
        $this->_name   = $name;
        $this->_boost  = $boost;
        $this->_filter = $filter;
        $this->_query  = $query;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * {@inheritDoc}
     */
    public function getBoost()
    {
        return $this->_boost;
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return QueryInterface::TYPE_FILTER;
    }

    /**
     * Query part of the filtered query.
     *
     * @return QueryInterface
     */
    public function getQuery()
    {
        return $this->_query;
    }

    /**
     * Filter part of the filtered query.
     *
     * @return QueryInterface
     */
    public function getFilter()
    {
        return $this->_filter;
    }
}
