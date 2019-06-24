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
 * @package     BinaryAnvil_InfinityTheme
 * @copyright   Copyright (c) 2017-2018 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\InfinityTheme\Helper;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Data\CollectionFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Catalog\Helper\Category as BaseClass;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Catalog\Model\Product\Type as ProductType;

class Catalog extends BaseClass
{
    /**
     * @const string Labels for product
     */
    const PRODUCT_LABEL_NEW = 'new';
    const PRODUCT_LABEL_SALE = 'sale';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    protected $stockItemRepository;

    /**
     * @var array
     */
    protected $newProduct = [];

    /**
     * Catalog constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\CollectionFactory $dataCollectionFactory
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     */
    public function __construct(
        Context $context,
        CategoryFactory $categoryFactory,
        StoreManagerInterface $storeManager,
        CollectionFactory $dataCollectionFactory,
        CategoryRepositoryInterface $categoryRepository,
        StockItemRepository $stockItemRepository,
        DateTime $dateTime
    ) {
        parent::__construct($context, $categoryFactory, $storeManager, $dataCollectionFactory, $categoryRepository);
        $this->dateTime = $dateTime;
        $this->stockItemRepository = $stockItemRepository;
    }

    /**
     * @param $categoryId
     * @return \Magento\Catalog\Api\Data\CategoryInterface|null
     */
    public function getCategoryById($categoryId)
    {
        try {
            return $this->categoryRepository->get($categoryId);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get product labels
     *
     * @param $product
     * @return array
     */
    public function getProductLabels($product)
    {
        $labels = [];

        if ($this->isNew($product)) {
            $labels[] = self::PRODUCT_LABEL_NEW;
        }
        if ($this->isSale($product)) {
            $labels[] = self::PRODUCT_LABEL_SALE;
        }

        return $labels;
    }

    /**
     * @param $product
     * @return bool
     */
    protected function isNew($product)
    {
        $fromDate = $product->getNewsFromDate();
        $toDate   = $product->getNewsToDate();

        if (!$fromDate) {
            return false;
        } else {
            if ($toDate) {
                $currentTime = $this->dateTime->gmtDate();
                $fromDate    = $this->dateTime->gmtDate(null, $fromDate);
                $toDate      = $this->dateTime->gmtDate(null, $toDate);

                $currentDateGreaterThanFromDate = (bool)($fromDate < $currentTime);
                $currentDateLessThanToDate      = (bool)($toDate > $currentTime);

                if ($currentDateGreaterThanFromDate && $currentDateLessThanToDate) {
                    return true;
                }
            } else {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $product
     * @return bool
     */
    protected function isSale($product)
    {
        if ($product->getTypeId() == ProductType::TYPE_SIMPLE) {
            return (bool) $product->getSpecialPrice();
        }

        $children = $product->getTypeInstance()->getUsedProducts($product);
        foreach ($children as $child) {
            if ($child->getSpecialPrice()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param  string $code
     * @return array
     */
    public function getOptionCountByAttributeCode($product, $code = null)
    {
        $codes = [];
        if ($product->getTypeId() == 'configurable') {
            $attributeCollection = $product->getTypeInstance()
                ->getConfigurableAttributes($product);

            if ($code) {
                $attributeCollection->getSelect()
                    ->join(
                        ['attributes' => 'eav_attribute'],
                        'attributes.attribute_id = main_table.attribute_id',
                        []
                    )
                    ->where(
                        'attributes.attribute_code=?',
                        $code
                    );
            }

            foreach ($attributeCollection as $attribute) {
                $codes[$attribute->getProductAttribute()->getAttributeCode()] = count($attribute->getOptions());
            }
            if ($code) {
                return isset($codes[$code]) ? $codes[$code] : null;
            }
        }
        return $codes;
    }

    /**
     * @param int $productId
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface
     */
    public function getStockItem($productId)
    {
        return $this->stockItemRepository->get($productId);
    }

    /**
     * Add product in new block
     *
     * @param int $productId
     */
    public function setNewProduct($productId)
    {
        $this->newProduct[] = $productId;
    }

    /**
     * Get New Product array
     *
     * @return array
     */
    public function getNewProduct()
    {
        return $this->newProduct;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->_urlBuilder->getUrl($route, $params);
    }

    /**
     * Return current URL with rewrites and additional parameters
     *
     * @param array $params Query parameters
     * @return string
     */
    public function getPagerUrl($params = [])
    {
        $urlParams = [];
        $urlParams['_current'] = true;
        $urlParams['_escape'] = false;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query'] = $params;
        return $this->getUrl('*/*/*', $urlParams);
    }

    /**
     * Retrieve widget options in json format
     *
     * @param array $customOptions Optional parameter for passing custom selectors from template
     * @return string
     */
    public function getWidgetOptionsJson(array $customOptions = [])
    {
        $options = [
            'url' => $this->getPagerUrl()
        ];
        $options = array_replace_recursive($options, $customOptions);
        return json_encode(['productListToolbarForm' => $options]);
    }
}
