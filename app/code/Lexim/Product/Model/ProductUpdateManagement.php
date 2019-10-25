<?php

namespace Lexim\Product\Model;

use Lexim\Product\Api\ProductUpdateManagementInterface as ProductApiInterface;

/**
 * Class ProductUpdateManagement
 * @package Lexim\Product\Model
 */
class ProductUpdateManagement implements ProductApiInterface
{

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * ProductUpdateManagement constructor.
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->productRepository = $productRepository;
        $this->logger = $logger;
    }

    /**
     * Updates the specified product from the request payload.
     *
     * @param mixed $products
     * @return boolean
     */
    public function updateProduct($products)
    {
        if (!empty($products)) {
            $messages = "";
            $error = false;
            foreach ($products as $product) {
                try {
                    $sku = $product['sku'];
                    $productObject = $this->productRepository->get($sku);
                    $qty = $product['qty'];
                    //$price = $product['price'];
                    //$productObject->setPrice($price);
                    $productObject->setStockData(
                        [
                            'is_in_stock' => 1,
                            'qty' => $qty
                        ]
                    );
                    try {
                        $this->productRepository->save($productObject);
                    } catch (\Exception $e) {
                        throw new StateException(__('Cannot save product.'));
                    }
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $messages[] = $product['sku'] . ' =>' . $e->getMessage();
                    $error = true;
                }
            }
            if ($error) {
                $this->writeLog(implode(" || ", $messages));
                return false;
            }
        }
        return true;
    }

    /* log for an API */
    public function writeLog($log)
    {
        $this->logger->info($log);
    }
}