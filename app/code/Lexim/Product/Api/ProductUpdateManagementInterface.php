<?php

namespace Lexim\Product\Api;

/**
 * @api
 * @since 101.0.0
 */
interface ProductUpdateManagementInterface
{
    /**
     * Updates the specified products in item array.
     *
     * @param $products
     * @return mixed
     */
    public function updateProduct($products);
}