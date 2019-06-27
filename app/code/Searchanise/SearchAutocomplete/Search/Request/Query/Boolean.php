<?php

namespace Searchanise\SearchAutocomplete\Search\Request\Query;

use Searchanise\SearchAutocomplete\Search\Request\QueryInterface;

/**
 * bool queries request implementation.
 */
class Boolean implements QueryInterface
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
     * @var QueryInterface[]
     */
    private $_must;

    /**
     * @var QueryInterface[]
     */
    private $_should;

    /**
     * @var QueryInterface[]
     */
    private $_mustNot;

    public function __construct(
        array $must = [],
        array $should = [],
        array $mustNot = [],
        $name = null,
        $boost = QueryInterface::DEFAULT_BOOST_VALUE
    ) {
        $this->_must    = $must;
        $this->_should  = $should;
        $this->_mustNot = $mustNot;
        $this->_boost   = $boost;
        $this->_name    = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return QueryInterface::TYPE_BOOLEAN;
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
     * Must clause queries.
     *
     * @return \Smile\ElasticsuiteCore\Search\Request\QueryInterface[]
     */
    public function getMust()
    {
        return $this->_must;
    }

    /**
     * Should clause queries.
     *
     * @return \Smile\ElasticsuiteCore\Search\Request\QueryInterface[]
     */
    public function getShould()
    {
        return $this->_should;
    }

    /**
     * Must not clause queries.
     *
     * @return \Smile\ElasticsuiteCore\Search\Request\QueryInterface[]
     */
    public function getMustNot()
    {
        return $this->_mustNot;
    }
}
