<?php

namespace Searchanise\SearchAutocomplete\Search\Adapter\Request\Query;

use Searchanise\SearchAutocomplete\Search\Request\QueryInterface;

/**
 * Build Searchanise queries from search request QueryInterface queries.
 */
class Builder implements BuilderInterface
{
    /**
     * @var array
     */
    private $_queryBuilderClasses = [
        QueryInterface::TYPE_FILTER     => 'Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\Builder\Filtered',
        QueryInterface::TYPE_MATCH      => 'Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\Builder\Match',
        QueryInterface::TYPE_BOOLEAN    => 'Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\Builder\Boolean',
        QueryInterface::TYPE_TERM       => 'Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\Builder\Term',
        QueryInterface::TYPE_TERMS      => 'Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\Builder\Terms',
        QueryInterface::TYPE_RANGE      => 'Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\Builder\Range',
    ];

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $_objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * {@inheritDoc}
     *
     * @see \Searchanise\SearchAutocomplete\Search\Adapter\Request\Query\BuilderInterface::buildQuery()
     */
    public function buildQuery(\Magento\Framework\Search\Request\QueryInterface $query)
    {
        $searchQuery = false;
        $builder = $this->getBuilder($query);

        if ($builder !== null) {
            $searchQuery = $builder->buildQuery($query);
        }

        return $searchQuery;
    }

    /**
     * Retrieve the builder used to build a query.
     *
     * @param QueryInterface $query Query to be built.
     *
     * @return BuilderInterface|null
     */
    public function getBuilder($query)
    {
        $builder = null;
        $queryType = $query->getType();

        if (isset($this->_queryBuilderClasses[$queryType])) {
            $builderClass = $this->_queryBuilderClasses[$queryType];
            $builder = $this->_objectManager->get($builderClass, ['builder' => $this]);
        }

        return $builder;
    }
}
