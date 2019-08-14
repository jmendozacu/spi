<?php
/**
 * Created by Samuel Kong
 * Date: Aug 12 2019
 */

namespace Lexim\Override\Block;

use Magento\Catalog\Helper\Product\ProductList;
use Magento\Catalog\Model\Product\ProductList\Toolbar as ToolbarModel;

/**
 * Class Toolbar
 * @package Lexim\Override\Block
 */
class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;


    /**
     * Toolbar constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param ToolbarModel $toolbarModel
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param ProductList $productListHelper
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Catalog\Model\Config $catalogConfig,
        ToolbarModel $toolbarModel,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        ProductList $productListHelper,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    )
    {
        $this->storeManager = $storeManager;
        parent::__construct(
            $context,
            $catalogSession,
            $catalogConfig,
            $toolbarModel,
            $urlEncoder,
            $productListHelper,
            $postDataHelper,
            $data
        );
    }


    /**
     * Set collection to pager
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @return $this|\Magento\Catalog\Block\Product\ProductList\Toolbar
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        $this->_collection->setCurPage($this->getCurrentPage());
        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }

//        echo $this->getCurrentOrder();
//         echo '<pre>';
//         var_dump($this->getAvailableOrders());
//         die;

        if ($this->getCurrentOrder()) {
            switch ($this->getCurrentOrder()) {
                // Lowest Price
                case 'price_asc':
                    $this->_collection->getSelect()->joinLeft(
                        'catalog_product_relation',
                        'catalog_product_relation.parent_id = e.row_id'
                    )
                        ->joinLeft(
                            'catalog_product_index_price',
                            'catalog_product_index_price.entity_id = catalog_product_relation.child_id',
                            'min(catalog_product_index_price.min_price) as sumTotal'
                        )
                        ->group('e.entity_id')
                        ->order('sumTotal asc');
                    break;

                // Highest Price
                case 'price_desc':
                    $this->_collection->getSelect()->joinLeft(
                        'catalog_product_relation',
                        'catalog_product_relation.parent_id = e.row_id'
                    )
                        ->joinLeft(
                            'catalog_product_index_price',
                            'catalog_product_index_price.entity_id = catalog_product_relation.child_id',
                            'max(catalog_product_index_price.max_price) as sumTotal'
                        )
                        ->group('e.entity_id')
                        ->order('sumTotal desc');
                    break;

                // Newest
                case 'created_at_desc':
                    $this->_collection->getSelect()->order('e.created_at desc');
                    break;

                // Oldest
                case 'created_at_asc':
                    $this->_collection->getSelect()->order('e.created_at asc');
                    break;

                // On Sale
                case 'on_sale':
                    $this->_collection->getSelect()->joinLeft(
                        'catalog_product_relation',
                        'catalog_product_relation.parent_id = e.row_id'
                    )
                        ->joinLeft(
                            'catalog_product_index_price',
                            'catalog_product_index_price.entity_id = catalog_product_relation.child_id ' .
                            ' AND catalog_product_index_price.final_price < catalog_product_index_price.price AND catalog_product_index_price.final_price > 0',
                            [
                                'sum(catalog_product_index_price.final_price) as sumFinalPrice',
                                'sum(catalog_product_index_price.price) as sumPrice'
                            ]
                        )
                        ->group('e.entity_id')
                        ->order('sumFinalPrice desc');
                    break;


                // Best selling
                case 'best_selling':
                    $this->_collection->getSelect()
                        ->joinLeft(
                            'sales_order_item',
                            'e.entity_id = sales_order_item.product_id',
                            array('qty_ordered' => 'SUM(sales_order_item.qty_ordered)')
                        )
                        ->group('e.entity_id')
                        ->order('qty_ordered desc');

//                    $this->_collection->getSelect()
//                        ->joinLeft(
//                            'sales_bestsellers_aggregated_yearly',
//                            'e.entity_id = sales_bestsellers_aggregated_yearly.product_id',
//                            'sales_bestsellers_aggregated_yearly.qty_ordered'
//                        )
//                        ->group('e.entity_id')
//                        ->order('sales_bestsellers_aggregated_yearly.qty_ordered desc');
                    break;


                // Top Rated
                case 'top_rated':
                    $this->_collection->getSelect()
                        ->joinLeft(
                            'review_entity_summary',
                            'e.entity_id = review_entity_summary.entity_pk_value',
                            array(
                                'review_entity_summary.rating_summary',
                                'review_entity_summary.store_id',
                                'sum_rate_point' => 'SUM(review_entity_summary.rating_summary)'
                            )
                        )
                        ->group('e.entity_id')
                        ->order('sum_rate_point desc');
                    break;

                // Default magento
                case 'position':
                    $this->_collection->addAttributeToSort(
                        $this->getCurrentOrder(),
                        $this->getCurrentDirection()
                    )->addAttributeToSort('entity_id', $this->getCurrentDirection());
                    break;

                default:
                    // $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection()); // default
                    // set default is newest
                    $this->_collection->getSelect()->order('e.created_at desc');
                    break;
            }
        }


        return $this;
    }

}