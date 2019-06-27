<?php

namespace Searchanise\SearchAutocomplete\Search\Request\Query;

use Searchanise\SearchAutocomplete\Search\Request\QueryInterface;

/**
 * Match query definition implementation.
 */
class Match implements QueryInterface
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
    private $_queryText;

    /**
     * @var string
     */
    private $_field;

    public function __construct(
        $queryText,
        $field,
        $name = null,
        $boost = QueryInterface::DEFAULT_BOOST_VALUE
    ) {
        $this->_name               = $name;
        $this->_queryText          = $queryText;
        $this->_field              = $field;
        $this->_boost              = $boost;
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
        return QueryInterface::TYPE_MATCH;
    }

    /**
     * Query match text.
     *
     * @return string
     */
    public function getQueryText()
    {
        return $this->_queryText;
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
}
