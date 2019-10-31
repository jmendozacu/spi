<?php
/**
 * Binary Anvil, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Binary Anvil, Inc. Software Agreement
 * that is bundled with this package in the file LICENSE_BAS.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.binaryanvil.com/software/license/
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@binaryanvil.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this software to
 * newer versions in the future. If you wish to customize this software for
 * your needs please refer to http://www.binaryanvil.com/software for more
 * information.
 *
 * @category    BinaryAnvil
 * @package     LayeredNavigation
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

/**
 * @codingStandardsIgnoreFile
 */

namespace BinaryAnvil\LayeredNavigation\Model\Layer\Filter;

use Magento\CatalogSearch\Model\Layer\Filter\Attribute as OriginClass;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder;
use Magento\Framework\Filter\StripTags;
use BinaryAnvil\LayeredNavigation\Model\Url\Builder;
use Magento\Framework\App\RequestInterface;
use BinaryAnvil\LayeredNavigation\Model\Layer\ItemCollectionProvider;
use BinaryAnvil\LayeredNavigation\Model\ResourceModel\Attribute\Option\Value as AttributeValues;

class Attribute extends OriginClass
{
    /**
     * @var \Magento\Framework\Filter\StripTags
     */
    private $tagFilter;

    /**
     * @var \BinaryAnvil\LayeredNavigation\Model\Url\Builder $urlBuilder
     */
    protected $urlBuilder;

    /**
     * @var \BinaryAnvil\LayeredNavigation\Model\Layer\ItemCollectionProvider $collectionProvider
     */
    protected $collectionProvider;

    /**
     * @var \Magento\Framework\App\RequestInterface $request
     */
    protected $request;

    /**
     * @var array
     */
    protected $customViewOptions = [];


    /**
     * Attribute constructor.
     * @param ItemFactory $filterItemFactory
     * @param StoreManagerInterface $storeManager
     * @param Layer $layer
     * @param DataBuilder $itemDataBuilder
     * @param StripTags $tagFilter
     * @param Builder $builder
     * @param ItemCollectionProvider $collectionProvider
     * @param RequestInterface $requestInterface
     * @param AttributeValues $attributeValues
     * @param array $data
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        ItemFactory $filterItemFactory,
        StoreManagerInterface $storeManager,
        Layer $layer,
        DataBuilder $itemDataBuilder,
        StripTags $tagFilter,
        Builder $builder,
        ItemCollectionProvider $collectionProvider,
        RequestInterface $requestInterface,
        AttributeValues $attributeValues,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $tagFilter,
            $data
        );
        $this->urlBuilder = $builder;
        $this->collectionProvider = $collectionProvider;
        $this->request = $requestInterface;
        $this->tagFilter = $tagFilter;
        $this->customViewOptions = $attributeValues->getAttributeOptionsValues($this->getAttributeModel()->getAttributeId());
    }

    /**
     * Apply attribute option filter to product collection
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function apply(RequestInterface $request)
    {
        $values = $this->urlBuilder->getValuesFromUrl($this->_requestVar);
        if (!$values) {
            return $this;
        }
        $productCollection = $this->getLayer()->getProductCollection();
        $this->applyToCollection($productCollection, $values);
        foreach ($values as $value) {
            if ($value == AttributeValues::CUSTOM_VIEW_ATTRIBUTE_OPTIONS_LINK) {
                $label = AttributeValues::CUSTOM_VIEW_ATTRIBUTE_OPTIONS_LABEL;
            } else {
                $label = $this->getOptionText($value);
            }

            $this->getLayer()->getState()->addFilter($this->_createItem($label, $value));
        }
        return $this;
    }

    /**
     * Apply filter to product collection
     *
     * @param $collection
     * @param $attributeValue
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function applyToCollection($collection, $attributeValue)
    {
        $attribute = $this->getAttributeModel();

        if(in_array(AttributeValues::CUSTOM_VIEW_ATTRIBUTE_OPTIONS_LINK, $attributeValue)) {
            $attributeValue = array_diff($attributeValue, [AttributeValues::CUSTOM_VIEW_ATTRIBUTE_OPTIONS_LINK]);
            $attributeValue = array_merge($attributeValue, $this->customViewOptions);
        }

        $collection->addFieldToFilter($attribute->getAttributeCode(), $attributeValue);
    }

    /**
     * Get data array for building attribute filter items
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getItemsData()
    {
        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $this->getLayer()->getProductCollection();
        $collection = $this->collectionProvider->getCollection($this->getCurrentCategory());
        $collection->updateSearchCriteriaBuilder();
        $this->getLayer()->prepareProductCollection($collection);

//        foreach ($productCollection->getAddedFilters() as $field => $condition) {
//            if ($this->getAttributeModel()->getAttributeCode() == $field) {
//                continue;
//            }
//            $collection->addFieldToFilter($field, $condition);
//        }

        $attribute = $this->getAttributeModel();
        $optionsFacetedData = $this->getFacetedData();
        $options = $attribute->getFrontend()->getSelectOptions();
        foreach ($options as $option) {
            if(empty($option['value'])) {
                continue;
            }
            if(isset($optionsFacetedData[$option['value']])){
                $count = $this->getOptionItemsCount($optionsFacetedData, $option['value']);
                $this->itemDataBuilder->addItemData(
                    $this->tagFilter->filter($option['label']),
                    $option['value'],
                    $count
                );
            }
        }
        return $this->itemDataBuilder->build();
    }

    /**
     * Get option items count by attribute option id
     *
     * @param $faceted
     * @param $key
     * @return int
     */
    protected function getOptionItemsCount($faceted, $key)
    {
        if (isset($faceted[$key]['count'])) {
            return $faceted[$key]['count'];
        }
        return 0;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getFacetedData()
    {
        $collection = $this->collectionProvider->getCollection($this->getCurrentCategory());
        $collection->updateSearchCriteriaBuilder();
        $collection->addCategoryFilter($this->getCurrentCategory());
        if ($this->getCurrentCategory()->getId() == $this->_storeManager->getStore()->getRootCategoryId()) {
            $collection->addSearchFilter($this->request->getParam('q'));
        }
        return $collection->getFacetedData($this->getAttributeModel()->getAttributeCode());
    }

    /**
     * @return \Magento\Catalog\Model\Category
     */
    protected function getCurrentCategory()
    {
        return $this->getLayer()->getCurrentCategory();
    }
}
