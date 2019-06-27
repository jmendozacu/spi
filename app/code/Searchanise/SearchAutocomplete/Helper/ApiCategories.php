<?php

namespace Searchanise\SearchAutocomplete\Helper;

/**
 * Categories helper for searchanise
 */
class ApiCategories extends \Magento\Framework\App\Helper\AbstractHelper
{
    // use id to hide categories
    private static $excludedCategories = [
    ];

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $catalogResourceModelCategoryCollectionFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $catalogResourceModelCategoryCollectionFactory
    ) {
        $this->storeManager = $storeManager;
        $this->catalogResourceModelCategoryCollectionFactory = $catalogResourceModelCategoryCollectionFactory;

        parent::__construct($context);
    }

    /**
     * Generate feed for category
     *
     * @param  \Magento\Catalog\Model\Category $category  Category
     * @param  \Magento\Store\Model\Store      $store     Store
     * @param  string                          $checkData Flag to check the data
     * @return array
     */
    public function generateCategoryFeed(
        \Magento\Catalog\Model\Category $category,
        \Magento\Store\Model\Store $store = null,
        $checkData = true
    ) {
        $item = [];

        if ($checkData
            && (empty($category)
            || !$category->getName()
            || !$category->getIsActive()
            || in_array($category->getId(), self::$excludedCategories))
        ) {
            return $item;
        }

        // Need for generate correct url.
        if (!empty($store)) {
            $category->getUrlInstance()->setScope($store->getId());
        }

        $item['id'] = $category->getId();

        try {
            $parentCategory = $category->getParentCategory();
            $item['parent_id'] = $parentCategory ? $parentCategory->getId() : 0;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $item['parent_id'] = 0;
        }
        $item['title'] = $category->getName();
        $item['link'] = $category->getUrl();

        // Fixme in the future
        // if need to add ico for image.
        // Show images without white field
        // Example: image 360 x 535 => 47 х 70
        // $flagKeepFrame = false;
        // $image =  Mage::helper('searchanise/ApiProducts')->getProductImageLink($category, $flagKeepFrame);
        // if ($image) {
        //     $imageLink = '' . $image;

        //     if ($imageLink != '') {
        //         $item['image_link'] = '' . $imageLink;
        //     }
        // }
        // end fixme
        $item['image_link'] = $category->getImageUrl();
        $item['summary'] = $category->getDescription();

        return $item;
    }

    /**
     * Return categories by category ids
     *
     * @param  mixed                      $categoryIds
     * @param  \Magento\Store\Model\Store $store
     * @return
     */
    public function getCategories(
        $categoryIds = \Searchanise\SearchAutocomplete\Model\Queue::NOT_DATA,
        \Magento\Store\Model\Store $store = null
    ) {
        static $arrCategories = [];
        $keyCategories = '';
        $storeId = !empty($store) ? $store->getId() : 0;
        $rootCategoryId = $this->storeManager->getStore($storeId)->getRootCategoryId();

        if (!empty($store)) {
            $this->storeManager->setCurrentStore($store);
        } else {
            $this->storeManager->setCurrentStore(null);
        }

        if (!empty($categoryIds)) {
            if (is_array($categoryIds)) {
                $keyCategories .= implode('_', $categoryIds);
            } else {
                $keyCategories .= $categoryIds;
            }
        }

        $keyCategories .= ':' .  $storeId;

        if (!isset($arrCategories[$keyCategories])) {
            $collection = $this->catalogResourceModelCategoryCollectionFactory->create();

            $collection
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('path', ['like' => "1/{$rootCategoryId}/%"]);

            if ($categoryIds !== \Searchanise\SearchAutocomplete\Model\Queue::NOT_DATA) {
                // Already exist automatic definition 'one value' or 'array'.
                $collection->addIdFilter($categoryIds);
            }

            $arrCategories[$keyCategories] = $collection->load();
        }

        return $arrCategories[$keyCategories];
    }

    /**
     * Generate categories feeds
     *
     * @param  unknown $categoryIds
     * @param  unknown $store
     * @param  string  $checkData
     * @return array[]
     */
    public function generateCategoriesFeed(
        $categoryIds = \Searchanise\SearchAutocomplete\Model\Queue::NOT_DATA,
        \Magento\Store\Model\Store $store = null,
        $checkData = true
    ) {
        $items = [];

        $categories = $this->getCategories($categoryIds, $store);

        if (!empty($categories)) {
            foreach ($categories as $category) {
                if ($item = $this->generateCategoryFeed($category, $store, $checkData)) {
                    $items[] = $item;
                }
            }
        }

        return $items;
    }

    /**
     * Returns mix/max category ids values
     *
     * @param  \Magento\Store\Model\Store $store
     * @return array(mix, max)
     */
    public function getMinMaxCategoryId(\Magento\Store\Model\Store $store = null)
    {
        $startId = 0;
        $endId = 0;

        $categoryStartCollection = $this->catalogResourceModelCategoryCollectionFactory->create()
            ->addAttributeToSort('entity_id', \Magento\Framework\Data\Collection::SORT_ORDER_ASC)
            ->setPageSize(1);

        if (!empty($store)) {
            $categoryStartCollection = $categoryStartCollection->setStoreId($store->getId());
        }

        $categoryStartCollection = $categoryStartCollection->load();

        $categoryEndCollection = $this->catalogResourceModelCategoryCollectionFactory->create()
            ->addAttributeToSort('entity_id', \Magento\Framework\Data\Collection::SORT_ORDER_DESC)
            ->setPageSize(1);

        if (!empty($store)) {
            $categoryEndCollection = $categoryEndCollection->setStoreId($store->getId());
        }

        $categoryEndCollection = $categoryEndCollection->load();

        if (!empty($categoryStartCollection)) {
            $categoryArr = $categoryStartCollection->toArray(['entity_id']);

            if (!empty($categoryArr)) {
                $firstItem = reset($categoryArr);
                $startId = $firstItem['entity_id'];
            }
        }

        if (!empty($categoryEndCollection)) {
            $categoryArr = $categoryEndCollection->toArray(['entity_id']);

            if (!empty($categoryArr)) {
                $firstItem = reset($categoryArr);
                $endId = $firstItem['entity_id'];
            }
        }

        return [$startId, $endId];
    }

    /**
     * Returns category ids from range
     *
     * @param  number                     $start Start category id
     * @param  number                     $end   End category id
     * @param  number                     $step  Step value
     * @param  \Magento\Store\Model\Store $store
     * @return array
     */
    public function getCategoryIdsFormRange($start, $end, $step, \Magento\Store\Model\Store $store = null)
    {
        $arrCategories = [];

        $categories = $this->catalogResourceModelCategoryCollectionFactory->create()
            ->addFieldToFilter('entity_id', ['from' => $start, 'to' => $end])
            ->setPageSize($step);

        if (!empty($store)) {
            $categories = $categories->setStoreId($store->getId());
        }

        $categories = $categories->load();

        if (!empty($categories)) {
            // Not used because 'arrCategories' comprising 'stock_item' field and is 'array(array())'
            // $arrCategories = $categories->toArray(array('entity_id'));
            foreach ($categories as $category) {
                $arrCategories[] = $category->getId();
            }
        }
        // It is necessary for save memory.
        unset($categories);

        return $arrCategories;
    }

    /**
     * Get children for categories
     *
     * @param  number $catId Category identifier
     * @return array
     */
    public function getAllChildrenCategories($catId)
    {
        $categoryIds = [];

        $categories = $this->catalogResourceModelCategoryCollectionFactory->create()
            ->setStoreId($this->storeManager->getStore()->getId())
            ->addFieldToFilter('entity_id', $catId)
            ->load();

        if (!empty($categories)) {
            foreach ($categories as $cat) {
                if (!empty($cat)) {
                    $categoryIds = $cat->getAllChildren(true);
                }
            }
        }

        return $categoryIds;
    }
}
