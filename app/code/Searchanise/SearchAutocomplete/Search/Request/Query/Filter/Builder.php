<?php

namespace Searchanise\SearchAutocomplete\Search\Request\Query\Filter;

use Searchanise\SearchAutocomplete\Search\Request\Query\QueryFactory;
use Searchanise\SearchAutocomplete\Search\Request\QueryInterface;

/**
 * Prepare filter condition from an array as used into addFieldToFilter.
 */
class Builder
{
    /**
     * @var QueryFactory
     */
    private $_queryFactory;

    /**
     * @var array
     */
    private $_mappedConditions = [
        'eq'     => 'values',
        'seq'    => 'values',
        'in'     => 'values',
        'from'   => 'gte',
        'moreq'  => 'gte',
        'gteq'   => 'gte',
        'to'     => 'lte',
        'lteq'   => 'lte',
        'like'   => 'queryText',
        'in_set' => 'values',
    ];

    /**
     * @var array
     */
    private $_unsupportedConditions = [
        'nin',
        'notnull',
        'null',
        'finset',
        'regexp',
        'sneq',
        'neq',
    ];

    /**
     * Constructor.
     *
     * @param QueryFactory $queryFactory Query factory (used to build subqueries).
     */
    public function __construct(QueryFactory $queryFactory)
    {
        $this->_queryFactory = $queryFactory;
    }

    /**
     * Prepare filter condition from an array as used into addFieldToFilter.
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     *
     * @param array $filters Filters to be built.
     *
     * @return QueryInterface
     */
    public function create(array $filters)
    {
        $queries = [];

        foreach ($filters as $fieldName => $condition) {
            if ($condition instanceof QueryInterface) {
                $queries[] = $condition;
            } else {
                $queries[] = $this->_prepareFieldCondition($fieldName, $condition);
            }
        }

        $filterQuery = current($queries);

        if (count($queries) > 1) {
            $filterQuery = $this->_queryFactory->create(QueryInterface::TYPE_BOOLEAN, ['must' => $queries]);
        }

        return $filterQuery;
    }

    /**
     * Transform the condition into a search request query object.
     *
     * @param FieldInterface $field     Filter field.
     * @param array|string   $condition Filter condition.
     *
     * @return QueryInterface
     */
    private function _prepareFieldCondition($field, $condition)
    {
        $queryType = QueryInterface::TYPE_TERMS;
        $condition = $this->_prepareCondition($condition);

        if (count(array_intersect(['gt', 'gte', 'lt', 'lte'], array_keys($condition))) >= 1) {
            $queryType = QueryInterface::TYPE_RANGE;
            $condition = ['bounds' => $condition];
        }

        if (in_array('queryText', array_keys($condition))) {
            $queryType = QueryInterface::TYPE_MATCH;
        }

        return $this->_queryFactory->create(
            $queryType,
            array_merge(
                $condition,
                [
                'field' => $field,
                ]
            )
        );
    }

    /**
     * Ensure the condition is supported and try to tranform it into a supported type.
     *
     * @param array|integer|string $condition Parsed condition.
     *
     * @return array
     */
    private function _prepareCondition($condition)
    {
        if (!is_array($condition)) {
            $condition = ['values' => [$condition]];
        }

        $conditionKeys = array_keys($condition);

        if (is_integer(current($conditionKeys))) {
            $condition = ['values' => $condition];
        }

        foreach ($condition as $key => $value) {
            if (in_array($key, $this->_unsupportedConditions)) {
                throw new \LogicException("Condition {$key} is not supported.");
            }

            if (isset($this->_mappedConditions[$key])) {
                $condition[$this->_mappedConditions[$key]] = $value;
                unset($condition[$key]);
            }
        }

        return $condition;
    }
}
