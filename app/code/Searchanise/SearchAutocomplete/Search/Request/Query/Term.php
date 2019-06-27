<?php

namespace Searchanise\SearchAutocomplete\Search\Request\Query;

use Searchanise\SearchAutocomplete\Search\Request\QueryInterface;

/**
 * Searchanise suite request term query.
 */
class Term extends AbstractQuery implements QueryInterface
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
     * @var string
     */
    private $_value;

    /**
     * @var string
     */
    private $_field;

    public function __construct($value, $field, $name = null, $boost = QueryInterface::DEFAULT_BOOST_VALUE)
    {
        $this->_name  = $name;
        $this->_value = $this->_prepareSourceValue($field, $value);
        $this->_field = $field;
        $this->_boost = $boost;
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
        return QueryInterface::TYPE_TERM;
    }

    /**
     * Search value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Search field.
     *
     * @return string
     */
    public function getField()
    {
        return $this->_field;
    }
}
