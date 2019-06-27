<?php

namespace Searchanise\SearchAutocomplete\Helper;

class ApiPages extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    private static $excludedPages = [
        'no-route', // 404 page
        'enable-cookies', // Enable Cookies
        'privacy-policy-cookie-restriction-mode', // Privacy Policy
        'service-unavailable', // 503 Service Unavailable
        'private-sales', // Welcome to our Exclusive Online Store
        'home', // Home
    ];

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Cms\Helper\Page
     */
    private $pageHelper;

    /**
     * @var \Magento\Cms\Model\ResourceModel\Page\CollectionFactory
     */
    private $cmsResourceModelPageCollectionFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Helper\Page $pageHelper,
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $cmsResourceModelPageCollectionFactory
    ) {
        $this->storeManager = $storeManager;
        $this->pageHelper = $pageHelper;
        $this->cmsResourceModelPageCollectionFactory = $cmsResourceModelPageCollectionFactory;

        parent::__construct($context);
    }

    /**
     * Generate feed for the page
     *
     * @param  \Magento\Cms\Model\Page    $page
     * @param  \Magento\Store\Model\Store $store
     * @param  string                     $checkData
     * @return array
     */
    public function generatePageFeed(
        \Magento\Cms\Model\Page $page,
        \Magento\Store\Model\Store $store = null,
        $checkData = true
    ) {
        $item = [];

        if ($checkData
            && (empty($page)
            || !$page->getId()
            || !$page->getTitle()
            || !$page->getIsActive()
            || in_array($page->getIdentifier(), self::$excludedPages))
        ) {
            return $item;
        }

        // Need for generate correct url.
        if (!empty($store)) {
            $this->storeManager->setCurrentStore($store);
        } else {
            $this->storeManager->setCurrentStore(0);
        }

        $item['id'] = $page->getId();
        $item['title'] = $page->getTitle();
        $item['link'] = $this->pageHelper->getPageUrl($page->getId());
        $item['summary'] = $page->getContent();

        return $item;
    }

    /**
     * Retruns pages by pages ids
     *
     * @param  mixed                      $pageIds Pages ids
     * @param  \Magento\Store\Model\Store $store   Stores
     * @return array
     */
    public function getPages(
        $pageIds = \Searchanise\SearchAutocomplete\Model\Queue::NOT_DATA,
        \Magento\Store\Model\Store $store = null
    ) {
        static $arrPages = [];

        $keyPages = '';

        if (!empty($pageIds)) {
            if (is_array($pageIds)) {
                $keyPages .= implode('_', $pageIds);
            } else {
                $keyPages .= $pageIds;
            }
        }

        $storeId = !empty($store) ? $store->getId() : 0;
        $keyPages .= ':' .  $storeId;

        if (!isset($arrPages[$keyPages])) {
            $collection = $this->cmsResourceModelPageCollectionFactory
                ->create()
                ->addStoreFilter($storeId);

            if ($pageIds !== \Searchanise\SearchAutocomplete\Model\Queue::NOT_DATA) {
                // Already exist automatic definition 'one value' or 'array'.
                $this->_addIdFilter($collection, $pageIds);
            }

            $arrPages[$keyPages] = $collection->load();
        }

        return $arrPages[$keyPages];
    }

    /**
     * Generate feed for the pages
     *
     * @param  mixed                      $pageIds   Page ids
     * @param  \Magento\Store\Model\Store $store     Store
     * @param  boolean                    $checkData
     * @return array
     */
    public function generatePagesFeed(
        $pageIds = \Searchanise\SearchAutocomplete\Model\Queue::NOT_DATA,
        \Magento\Store\Model\Store $store = null,
        $checkData = true
    ) {
        $items = [];

        $pages = $this->getPages($pageIds, $store);

        if (!empty($pages)) {
            foreach ($pages as $page) {
                $item = $this->generatePageFeed($page, $store, $checkData);

                if (!empty($item)) {
                    $items[] = $item;
                }
            }
        }

        return $items;
    }

    /**
     * Returns mix/max page ids values
     *
     * @param  \Magento\Store\Model\Store $store
     * @return array(mix, max)
     */
    public function getMinMaxPageId(\Magento\Store\Model\Store $store = null)
    {
        $startId = 0;
        $endId = 0;

        $pageStartCollection = $this->cmsResourceModelPageCollectionFactory->create()->setPageSize(1);
        $this->_addAttributeToSort($pageStartCollection, 'page_id');

        if (!empty($store)) {
            $pageStartCollection = $pageStartCollection->addStoreFilter($store->getId());
        }

        $pageStartCollection = $pageStartCollection->load();

        $pageEndCollection = $this->cmsResourceModelPageCollectionFactory->create()->setPageSize(1);

        $this->_addAttributeToSort($pageEndCollection, 'page_id', \Magento\Framework\Data\Collection::SORT_ORDER_DESC);

        if (!empty($store)) {
            $pageEndCollection = $pageEndCollection->addStoreFilter($store->getId());
        }

        $pageEndCollection = $pageEndCollection->load();

        if (!empty($pageStartCollection)) {
            $pageArr = $pageStartCollection->toArray(['page_id']);

            if (!empty($pageArr)) {
                $firstItem = reset($pageArr);
                $startId = $firstItem['page_id'];
            }
        }

        if (!empty($pageEndCollection)) {
            $pageArr = $pageEndCollection->toArray(['page_id']);

            if (!empty($pageArr)) {
                $firstItem = reset($pageArr);
                $endId = $firstItem['page_id'];
            }
        }

        return [$startId, $endId];
    }

    /**
     * Returns page ids from range
     *
     * @param  number                     $start Start page id
     * @param  number                     $end   End page id
     * @param  number                     $step  Step value
     * @param  \Magento\Store\Model\Store $store
     * @return array
     */
    public function getPageIdsFormRange($start, $end, $step, \Magento\Store\Model\Store $store = null)
    {
        $arrPages = [];

        $pages = $this->cmsResourceModelPageCollectionFactory
            ->create()
            ->addFieldToFilter('page_id', ['from' => $start, 'to' => $end])
            ->setPageSize($step);

        if (!empty($store)) {
            $pages = $pages->addStoreFilter($store->getId());
        }

        $pages = $pages->load();

        if (!empty($pages)) {
            // Not used because 'arrPages' comprising 'stock_item' field and is 'array(array())'
            // $arrPages = $pages->toArray(array('page_id'));
            foreach ($pages as $page) {
                $arrPages[] = $page->getId();
            }
        }
        // It is necessary for save memory.
        unset($pages);

        return $arrPages;
    }

    /**
     * Add Id filter
     *
     * @param  \Magento\Cms\Model\ResourceModel\Page\Collection $collection
     * @param  array                                            $pageIds
     * @return \Magento\Cms\Model\ResourceModel\Page\Collection
     */
    private function _addIdFilter(\Magento\Cms\Model\ResourceModel\Page\Collection &$collection, $pageIds)
    {
        if (is_array($pageIds)) {
            if (empty($pageIds)) {
                $condition = '';
            } else {
                $condition = ['in' => $pageIds];
            }
        } elseif (is_numeric($pageIds)) {
            $condition = $pageIds;
        } elseif (is_string($pageIds)) {
            $ids = explode(',', $pageIds);

            if (empty($ids)) {
                $condition = $pageIds;
            } else {
                $condition = ['in' => $ids];
            }
        }

        return $collection->addFieldToFilter('page_id', $condition);
    }

    /**
     * Add attribute to sort order
     *
     * @param  \Magento\Cms\Model\ResourceModel\Page\Collection Collection
     * @param  string                                                      $attribute
     * @param  string                                                      $dir
     * @return \Magento\Cms\Model\ResourceModel\Page\Collection
     */
    private function _addAttributeToSort(
        \Magento\Cms\Model\ResourceModel\Page\Collection &$collection,
        $attribute,
        $dir = \Magento\Framework\Data\Collection::SORT_ORDER_ASC
    ) {
        if (!is_string($attribute)) {
            return $collection;
        }

        return $collection->setOrder($attribute, $dir);
    }
}
