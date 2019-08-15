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
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\InfinityTheme\Plugin\Magento\CatalogSearch\Model\Layer\Category;

use BinaryAnvil\InfinityTheme\Helper\Catalog;
use BinaryAnvil\InfinityTheme\Model\Product\NewProducts;
use Magento\Catalog\Model\Layer\ItemCollectionProviderInterface;

class ItemCollectionProvider
{
    /**
     * @var \BinaryAnvil\InfinityTheme\Helper\Catalog $catalogHelper
     */
    protected $catalogHelper;

    /**
     * @var \BinaryAnvil\InfinityTheme\Model\Product\NewProducts $newProductCollection
     */
    protected $newProductCollection;

    /**
     * @var array
     */
    protected $newProducts = [];

    /**
     * Layer constructor.
     *
     * @param \BinaryAnvil\InfinityTheme\Helper\Catalog $catalogHelper
     * @param \BinaryAnvil\InfinityTheme\Model\Product\NewProducts $newProduct
     */
    public function __construct(Catalog $catalogHelper, NewProducts $newProduct)
    {
        $this->catalogHelper = $catalogHelper;
        $this->newProductCollection = $newProduct;
    }

    /**
     * Get new products
     *
     * @param \Magento\CatalogSearch\Model\Layer\Category\ItemCollectionProvider $subject
     * @param $category
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeGetCollection($subject, $category)
    {
        if ($subject instanceof ItemCollectionProviderInterface && !$this->newProducts) {
            $newProducts = $this->newProductCollection->getNewProductsCollection($category);
            $this->newProducts = $this->getNewProductsArray($newProducts);
        }
    }

    /**
     * Removed new prodcuts from collection
     *
     * @param $subject
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetCollection($subject, $collection)
    {
        if ($subject instanceof ItemCollectionProviderInterface && count($this->newProducts)) {
            // $collection->addAttributeToFilter('entity_id', ['nin' => $this->newProducts]);
            return $collection;
        }
        return $collection;
    }

    /**
     * Get New Products array
     *
     * @param $newProducts
     * @return array
     */
    protected function getNewProductsArray($newProducts)
    {
        $products = [];
        foreach ($newProducts as $product) {
            $products[] = $product->getId();
        }
        return $products;
    }
}
