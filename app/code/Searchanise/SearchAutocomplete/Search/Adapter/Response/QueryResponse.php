<?php

namespace Searchanise\SearchAutocomplete\Search\Adapter\Response;

use \Searchanise\SearchAutocomplete\Model\Request as SearchaniseRequest;
use \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
    as catalogProductAttributeCollectionFactory;

/**
 * Searchanise response adapter
 */
class QueryResponse implements \Magento\Framework\Search\ResponseInterface
{
    /**
     * Document Collection
     *
     * @var Document[]
     */
    protected $documents = [];

    /**
     * @var integer
     */
    protected $count = 0;

    /**
     * Aggregation Collection
     *
     * @var AggregationInterface
     */
    protected $aggregations;

    /**
     * @var array
     */
    private $suggestions = [];

    /**
     * @var \Magento\CatalogSearch\Helper\Data
     */
    private $catalogSearchHelper;

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var catalogProductAttributeCollectionFactory
     */
    private $catalogProductAttributeCollectionFactory;

    public function __construct(
        DocumentFactory $documentFactory,
        AggregationFactory $aggregationFactory,
        SearchaniseRequest $searchaniseRequest,
        \Magento\CatalogSearch\Helper\Data $catalogSearchHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        catalogProductAttributeCollectionFactory $catalogProductAttributeCollectionFactory
    ) {
        $this->_prepareDocuments($searchaniseRequest, $documentFactory);
        $this->_prepareAggregations($searchaniseRequest, $aggregationFactory);

        $this->catalogSearchHelper = $catalogSearchHelper;
        $this->storeManager = $storeManager;
        $this->catalogProductAttributeCollectionFactory = $catalogProductAttributeCollectionFactory;

        if ($searchaniseRequest) {
            $this->suggestions = $searchaniseRequest->getSuggestions();
            // TODO: Shouldn't be here
            $this->renderSuggestions();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->documents);
    }

    /**
     * {@inheritDoc}
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    public function getRawAggregations()
    {
        $excluded_buckets = [
            'reviews_average_score_titles' . \Magento\CatalogSearch\Model\Search\RequestGenerator::BUCKET_SUFFIX,
        ];

        $rawAggregations = [];

        if ($this->aggregations !== null) {
            $bukets = $this->aggregations->getBuckets();

            foreach ($bukets as $bucket) {
                if (in_array($bucket->getName(), $excluded_buckets)) {
                    continue;
                }

                $bucketValues = [];

                foreach ($bucket->getValues() as $value) {
                    $metrics = $value->getMetrics();
                    $bucketValues[$metrics['value']] = [
                        'value' => $metrics['value'],
                        'count' => $metrics['count'],
                    ];
                }

                if ($bucket->getName() == 'category_ids' . \Magento\CatalogSearch\Model\Search\RequestGenerator::BUCKET_SUFFIX) {
                    $rawAggregations['category' . \Magento\CatalogSearch\Model\Search\RequestGenerator::BUCKET_SUFFIX] = $bucketValues;
                } else {
                    $rawAggregations[$bucket->getName()] = $bucketValues;
                }
            }
        }

        // Magento2 process all available facets but searchanise returns only available facets for current data
        // So we have to add empty facets manually
        $attributes = $this->catalogProductAttributeCollectionFactory
            ->create()
            ->setItemObjectClass(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class)
            ->addIsFilterableInSearchFilter()
            ->load();

        foreach ($attributes as $attribute) {
            $bucket = $attribute->getAttributeCode() . \Magento\CatalogSearch\Model\Search\RequestGenerator::BUCKET_SUFFIX;

            if (!isset($rawAggregations[$bucket])) {
                $rawAggregations[$bucket] = [];
            }
        }

        // Category bucket is required for Magento 2
        if (!isset($rawAggregations['category' . \Magento\CatalogSearch\Model\Search\RequestGenerator::BUCKET_SUFFIX])) {
            $rawAggregations['category' . \Magento\CatalogSearch\Model\Search\RequestGenerator::BUCKET_SUFFIX] = [];
        }

        return $rawAggregations;
    }

    public function getRawDocuments()
    {
        $rawDocuments = [];

        foreach ($this->documents as $document) {
            $rawDocuments[$document->getId()] = [
                'entity_id' => $document->getId(),
                'score' => 0.0,
            ];
        }

        return $rawDocuments;
    }

    /**
     * Return documents
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Return suggestions list
     *
     * @return array
     */
    public function getSuggestions()
    {
        return $this->suggestions;
    }

    /**
     * Render suggestions
     *
     * @return boolean
     */
    public function renderSuggestions()
    {
        if (empty($this->suggestions) || $this->count() > 0) {
            return false;
        }

        $suggestionsMaxResults = \Searchanise\SearchAutocomplete\Helper\ApiSe::getSuggestionsMaxResults();

        $message = __('Did you mean: ');
        $link = [];
        $textFind = $this->catalogSearchHelper->getEscapedQueryText();
        $count_sug = 0;

        foreach ($this->getSuggestions() as $k => $sug) {
            if ((!empty($sug)) && ($sug != $textFind)) {
                $link[] = '<a href="' . $this->_getUrlSuggestion($sug). '">' . $sug .'</a>';
                $count_sug++;
            }

            if ($count_sug >= $suggestionsMaxResults) {
                break;
            }
        }

        if (!empty($link)) {
            $this->catalogSearchHelper->addNoteMessage($message . implode(',', $link) . '?');
        }

        return true;
    }

    /**
     * Build buckets from raw search response.
     *
     * @param SearchaniseRequest $searchRequest      Engine processed request
     * @param AggregationFactory $aggregationFactory Aggregation factory.
     *
     * @return void
     */
    private function _prepareAggregations(SearchaniseRequest $searchRequest, AggregationFactory $aggregationFactory)
    {
        if (!$searchRequest) {
            return;
        }

        $this->aggregations = $aggregationFactory->create($searchRequest->getFacets());
    }

    /**
     * Build document list from the engine raw search response.
     *
     * @param SearchaniseRequest $searchResponse  Engine processed request
     * @param DocumentFactory    $documentFactory Document factory
     *
     * @return void
     */
    private function _prepareDocuments(SearchaniseRequest $searchRequest, DocumentFactory $documentFactory)
    {
        if (!$searchRequest) {
            return;
        }

        $this->documents = [];

        $items = $searchRequest->getProductIds();

        if (!empty($items)) {
            foreach ($items as $item) {
                $this->documents[] = $documentFactory->create($item);
            }
        }

        $this->count = $searchRequest->getTotalProduct();
    }

    /**
     * Returns suggestion link
     *
     * @param  string $suggestion
     * @return string
     */
    private function _getUrlSuggestion($suggestion)
    {
        $pager = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Theme\Block\Html\Pager');

        $query = [
            'q'                         => $suggestion,
            $pager->getPageVarName()    => null // exclude current page from urls
        ];

        return $this->storeManager->getStore()->getUrl(
            '*/*/*',
            [
            '_current'      => true,
            '_use_rewrite'  => true,
            '_query'        => $query
            ]
        );
    }
}
