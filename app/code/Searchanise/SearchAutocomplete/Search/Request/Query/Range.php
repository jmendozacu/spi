<?php

namespace Searchanise\SearchAutocomplete\Search\Request\Query;

use Searchanise\SearchAutocomplete\Search\Request\QueryInterface;

/**
 * Range query implementation.
 */
class Range implements QueryInterface
{
    /**
     * @var integer
     */
    private $_boost;

    /**
     * @var string
     */
    private $_name;

    /**
     * @var string
     */
    private $_field;

    /**
     * @var array
     */
    private $_bounds;

    /**
     * Constructor.
     *
     * @param string  $field  Query field.
     * @param array   $bounds Range filter bounds (authorized entries : gt, lt, lte, gte).
     * @param string  $name   Query name.
     * @param integer $boost  Query boost.
     */
    public function __construct($field, array $bounds = [], $name = null, $boost = QueryInterface::DEFAULT_BOOST_VALUE)
    {
        $this->_name  = $name;
        $this->_boost = $boost;
        $this->_field = $field;
        $this->_bounds = $bounds;
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
        return QueryInterface::TYPE_RANGE;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Query field.
     *
     * @return string
     */
    public function getField()
    {
        return $this->_field;
    }

    /**
     * Range filter bounds.
     *
     * @return array
     */
    public function getBounds()
    {
        return $this->_bounds;
    }
}
