<?php

namespace Searchanise\SearchAutocomplete\Search\Adapter;

use \Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder as AggregationBuilder;
use \Magento\Framework\Search\Adapter\Mysql\Mapper;
use \Magento\Framework\Search\Adapter\Mysql\ResponseFactory;
use \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory;
use \Magento\Framework\App\ResourceConnection;

/**
 * Searchanise Search Adapter.
 */
class Adapter extends \Magento\Framework\Search\Adapter\Mysql\Adapter
{
    /**
     * @var \Searchanise\SearchAutocomplete\Helper\Data
     */
    private $searchaniseHelper;

    /**
     * @var \Simtech\Search\Adapter\Request\Mapper
     */
    private $requestMapper;

    /**
     * @var \Searchanise\SearchAutocomplete\Search\Adapter\Response\QueryResponseFactory
     */
    private $searchaniseResponseFactory;

    /**
     * @var \Searchanise\SearchAutocomplete\Helper\Logger
     */
    private $loggerHelper;

    /**
     * @var \Searchanise\SearchAutocomplete\Model\Configuration
     */
    private $configuration;

    public function __construct(
        Mapper $mapper,
        ResponseFactory $responseFactory,
        ResourceConnection $resourceConnection,
        AggregationBuilder $aggregationBuilder,
        TemporaryStorageFactory $temporaryStorageFactory,

        \Searchanise\SearchAutocomplete\Search\Adapter\Request\Mapper $requestMapper,
        \Searchanise\SearchAutocomplete\Search\Adapter\Response\QueryResponseFactory $searchaniseResponseFactory,
        \Searchanise\SearchAutocomplete\Helper\Data $searchaniseHelper,
        \Searchanise\SearchAutocomplete\Helper\Logger $loggerHelper,
        \Searchanise\SearchAutocomplete\Model\Configuration $configuration
    ) {
        $this->searchaniseHelper = $searchaniseHelper;
        $this->searchaniseResponseFactory = $searchaniseResponseFactory;
        $this->requestMapper = $requestMapper;
        $this->loggerHelper = $loggerHelper;
        $this->configuration = $configuration;

        parent::__construct(
            $mapper,
            $responseFactory,
            $resourceConnection,
            $aggregationBuilder,
            $temporaryStorageFactory
        );
    }

    /**
     * Test is full text search request
     * 
     * @param \Magento\Framework\Search\RequestInterface $request
     * @return boolean
     */
    private function isSearchaniseQuery(\Magento\Framework\Search\RequestInterface $request)
    {
        if ($this->configuration->isSearchaniseSearchEnabled()
            && $request->getIndex() == 'catalogsearch_fulltext'
            && ($request->getName() == 'quick_search_container' || $request->getName() == 'advanced_search_container')
        ) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function query(\Magento\Framework\Search\RequestInterface $request)
    {
        $isSearchaniseQuery = $this->isSearchaniseQuery($request);

        if ($isSearchaniseQuery) {
            try {
                $searchRequest = $this->searchaniseHelper->search([
                    'type'      => $request->getName(),
                    'request'   => $this->requestMapper->buildSearchRequest($request),
                ]);
            } catch (\Exception $e) {
                $this->loggerHelper->log($e->getMessage());
                $searchRequest = null;
            }

            if ($searchRequest !== null  && !$searchRequest->hasError()) {
                $response =  $this->searchaniseResponseFactory->create(['searchaniseRequest' => $searchRequest]);
                $documents = $response->getRawDocuments();
                $aggregations = $response->getRawAggregations();
            } else {
                $isSearchaniseQuery = false;
            }
        }

        if ($isSearchaniseQuery) {
            return $this->responseFactory->create([
                'documents' => $documents,
                'aggregations' => $aggregations,
            ]);
        } else {
            // Native request
            return parent::query($request);
        }
    }
}
