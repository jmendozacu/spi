<?php

namespace Searchanise\SearchAutocomplete\Search\Adapter\Response;

/**
 * Searhanise aggregations response builder.
 */
class AggregationFactory extends \Magento\Framework\Search\Adapter\Mysql\AggregationFactory
{
    /**
     * {@inheritDoc}
     */
    public function create(array $rawAggregation)
    {
        $aggregations = $this->_preprocessAggregations($rawAggregation);
        return parent::create($aggregations);
    }

    /**
     * Derefences children aggregations (nested and filter) while they have the same name.
     *
     * @param array $rawAggregation Aggregations response.
     * @return array
     */
    private function _preprocessAggregations(array $rawAggregation)
    {
        $processedAggregations = [];

        foreach ($rawAggregation as $bucketName => $aggregation) {
            if (!empty($aggregation['attribute'])) {
                $bucketName = strtolower($aggregation['attribute']) . \Magento\CatalogSearch\Model\Search\RequestGenerator::BUCKET_SUFFIX;
            } elseif (!empty($aggregation['title'])) {
                $bucketName = strtolower($aggregation['title']) . \Magento\CatalogSearch\Model\Search\RequestGenerator::BUCKET_SUFFIX;
            }

            if (strtolower($aggregation['attribute']) == 'category_ids') {
                // category_bucket name is used in magento
                $bucketName = 'category' . \Magento\CatalogSearch\Model\Search\RequestGenerator::BUCKET_SUFFIX;
            }

            if (!empty($aggregation['buckets'])) {
                foreach ($aggregation['buckets'] as $key => $currentBuket) {
                    if (!is_numeric($currentBuket['value'])) {
                        $currentBuket['value'] = $this->_getAttributeOptionId(strtolower($aggregation['attribute']), $currentBuket['value']);
                    }

                    $processedAggregations[$bucketName][$currentBuket['value']] = [
                        'value' => $currentBuket['value'],
                        'count' => $currentBuket['count'],
                    ];
                }
            }
        }

        return $processedAggregations;
    }

    /**
     * Get attribute option id by label
     *
     * @param  string $attribute Attribute name
     * @param  string $label     Option label
     * @return numeric
     */
    private function _getAttributeOptionId($attribute, $label)
    {
        // Hack for price, since Searchanise returns price in 60,70 format but magento requires 60_70
        if ($attribute == 'price') {
            $label = implode('_', explode(',', $label));
        }

        $productResource = $this->objectManager->get('Magento\Catalog\Model\ResourceModel\ProductFactory')->create();
        $attr = $productResource->getAttribute($attribute);

        if ($attr && $attr->usesSource()) {
            return $attr->getSource()->getOptionId($label);
        }

        return $label;
    }
}
