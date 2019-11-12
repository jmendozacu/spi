<?php declare(strict_types=1);

namespace Lexim\InventoryIntegration\Model;

use Exception;
use Lexim\InventoryIntegration\Model\Api\Rest\Service;
use Lexim\InventoryIntegration\Model\Source\Config;
use Lexim\InventoryIntegration\Model\Api\ServiceInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Psr\Log\LoggerInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;

class Inventory
{
    const BATCH_SIZE = 200;
    /**
     * @var Service
     */
    private $service;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var LoggerInterface $log
     */
    private $log;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * Inventory constructor.
     * @param Service $service
     * @param LoggerInterface $logger
     * @param Config $config
     * @param CollectionFactory $productCollectionFactory
     * @param ProductFactory $productFactory
     * @param StockRegistryInterface $stockRegistry
     */
    public function __construct(
        Service $service,
        LoggerInterface $logger,
        Config $config,
        CollectionFactory $productCollectionFactory,
        ProductFactory $productFactory,
        StockRegistryInterface $stockRegistry
    ) {
        $this->config = $config;
        $this->service = $service;
        $this->log = $logger;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productFactory = $productFactory;
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * @param $sku
     */
    public function updateProduct($sku)
    {
        $url = $this->config->getIntegrationUrl() . '?y=' . $sku;
        $this->service->setHeader('Content-Type', 'application/json');
        try {
            $response = $this->service->makeRequest($url, '', ServiceInterface::GET);
            if (isset($response['inv'])) {
                foreach ($response['inv'] as $inv) {
                    $skuSource = strtoupper($inv['u']);
                    $qty = $inv['av'];
                    if (empty($skuSource) || ($qty < 0)) {
                        continue;
                    }
                    $product = $this->productFactory->create();
                    $product->load($product->getIdBySku($skuSource));
                    // For 2.2.5
                    if ($product->getId()) {
                        $stockItem = $this->stockRegistry->getStockItemBySku($sku);
                        $stockItem->setQty($qty);
                        $this->stockRegistry->updateStockItemBySku($sku, $stockItem);
                    } else {
                        $this->log->error(sprintf('Product id %s doesn\'t exist ', $sku));
                    }
                }
            }
        } catch (Api\Exception $exception) {
            $this->log->error('Integration updated product failed. Sku: '. $sku . ' ' . $exception->getMessage());
        } catch (Exception $e) {
            $this->log->error('Integration updated product failed. Sku: '. $sku . ' ' . $e->getMessage());
        }
    }

    /**
     * Product list
     */
    public function updateProductList()
    {
        /** @var Collection $collection */
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToFilter('type_id', ['eq' => Type::TYPE_SIMPLE]);
        $collection->setFlag('has_stock_status_filter', true);
        $collection->setPageSize(self::BATCH_SIZE);
        $totalsPage = $collection->getLastPageNumber();
        $currentPage = 1;
        if ($collection->getSize() > 0) {
            do {
                $collection->setCurPage($currentPage);
                foreach ($collection->getItems() as $item) {
                    /** @var Product $item */
                    $this->updateProduct($item->getSku());
                };
                $currentPage++;
                $collection->clear();
            } while ($currentPage <= $totalsPage);
        }
    }
}
