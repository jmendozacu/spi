<?php

namespace Searchanise\SearchAutocomplete\Search\Request\SortOrder;

use Searchanise\SearchAutocomplete\Search\Request\SortOrderInterface;

/**
 * Normal sort order implementation.
 */
class Standard implements SortOrderInterface
{
    /**
     * @var string
     */
    private $_name;

    /**
     * @var string
     */
    private $f_ield;

    /**
     * @var string
     */
    private $_direction;

    public function __construct($field, $direction, $name = null)
    {
        $this->_name      = $name;
        $this->_field     = $field;
        $this->_direction = $direction;
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
    public function getField()
    {
        return $this->_field;
    }

    /**
     * {@inheritDoc}
     */
    public function getDirection()
    {
        return $this->_direction;
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return SortOrderInterface::TYPE_STANDARD;
    }
}
