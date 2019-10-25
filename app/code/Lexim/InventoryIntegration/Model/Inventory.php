<?php

// declare(strict_types=1);

namespace Lexim\InventoryIntegration\Model;

use Exception;
use Lexim\InventoryIntegration\Model\Api\Rest\Service;
use Lexim\InventoryIntegration\Model\Source\Config;
use Lexim\InventoryIntegration\Model\Api\ServiceInterface;
use Lexim\InventoryIntegration\Model\ResourceModel\IntegrationTracking as IntegrationTrackingResourceModel;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku;
use Psr\Log\LoggerInterface;

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
     * @var SourceItemsSaveInterface
     */
    protected $sourceItemsSaveInterface;

    /**
     * @var SourceItemInterfaceFactory
     */
    protected $sourceItemFactory;

    /**
     * @var GetSourceItemsBySku
     */
    private $sourceDataBySku;

    /**
     * @var LoggerInterface $log
     */
    private $log;

    /**
     * @var IntegrationTrackingResourceModel
     */
    private $integrationTracking;

    /**
     * Inventory constructor.
     * @param Service $service
     * @param LoggerInterface $logger
     * @param Config $config
     * @param CollectionFactory $productCollectionFactory
     * @param ProductFactory $productFactory
     * @param SourceItemsSaveInterface $sourceItemsSaveInterface
     * @param IntegrationTrackingResourceModel $integrationTracking
     * @param SourceItemInterfaceFactory $sourceItemFactory
     * @param GetSourceItemsBySku $sourceDataBySku
     */
    public function __construct(
        Service $service,
        LoggerInterface $logger,
        Config $config,
        CollectionFactory $productCollectionFactory,
        ProductFactory $productFactory,
        SourceItemsSaveInterface $sourceItemsSaveInterface,
        IntegrationTrackingResourceModel $integrationTracking,
        SourceItemInterfaceFactory $sourceItemFactory,
        GetSourceItemsBySku $sourceDataBySku
    )
    {
        $this->config = $config;
        $this->service = $service;
        $this->log = $logger;
        $this->integrationTracking = $integrationTracking;
        $this->sourceItemsSaveInterface = $sourceItemsSaveInterface;
        $this->sourceItemFactory = $sourceItemFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productFactory = $productFactory;
        $this->sourceDataBySku = $sourceDataBySku;
    }

    /**
     * @param string $sku
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
                    $product = $this->productFactory->create();
                    $product->load($product->getIdBySku($skuSource));
                    if ($product->getId()) {
                        $sourceItems = $this->sourceDataBySku->execute($skuSource);
                        foreach ($sourceItems as &$sourceItem) {
                            $sourceItem->setQuantity($qty);
                        }
                        $this->sourceItemsSaveInterface->execute($sourceItems);
                        $this->log->info(
                            sprintf("Integration updated product successfully. Product sku: %s", $product->getSku())
                        );
                    } else {
                        $this->log->error(sprintf('Product id %s doesn\'t exist ', $sku));
                    }
                }
            }
        } catch (Api\Exception $exception) {
            $this->log->error('Integration updated product failed. ' . $exception->getMessage());
        } catch (Exception $e) {
            $this->log->error('Integration updated product failed. ' . $e->getMessage());
        }
    }

    /**
     * @throws LocalizedException
     */
    public function updateProductList()
    {
        /** @var Collection $collection */
        $collection = $this->productCollectionFactory->create();
        $collection->setFlag('has_stock_status_filter', true);
        $id = $this->integrationTracking->getProductId();
        $newCollection = clone $collection;
        $lastItem = $newCollection->getLastItem();
        //If the current id is the last item. Should reset the value to zero
        if ($lastItem->getId() <= $id) {
            $id = 0;
        }
        $collection->addAttributeToFilter('entity_id', ['gteq' => $id]);
        $collection->setPageSize(self::BATCH_SIZE);
        $productId = $id;
        foreach ($collection->getItems() as $item) {
            /** @var Product $item */
            $this->updateProduct($item->getSku());
            $productId = $item->getId();
        };
        //Update tracking product id
        $this->integrationTracking->updateCurrentProductId($productId);
    }
}
