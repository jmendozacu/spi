<?php

namespace Searchanise\SearchAutocomplete\Search\Request\Query;

use Searchanise\SearchAutocomplete\Search\Request\QueryInterface;

/**
 * Searchanize suite request terms query.
 */
class Terms extends Term
{
    public function __construct($values, $field, $name = null, $boost = QueryInterface::DEFAULT_BOOST_VALUE)
    {
        if (!is_array($values)) {
            $values = explode(',', $values);
        }

        parent::__construct($values, $field, $name, $boost);
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return QueryInterface::TYPE_TERMS;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->getValue();
    }
}
