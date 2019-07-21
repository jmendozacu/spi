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
 * @package     BinaryAnvil_Toolbar
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\Toolbar\Plugin\Catalog\Block\Product\ProductList;

use Magento\Catalog\Block\Product\ProductList\Toolbar as OriginalClass;
use BinaryAnvil\Toolbar\Model\Sorting;

class Toolbar
{
    /**
     * @var \BinaryAnvil\Toolbar\Model\Sorting
     */
    private $toolbarSorting;

    /**
     * Toolbar plugin constructor
     *
     * @param Sorting $toolbarSorting
     */
    public function __construct(Sorting $toolbarSorting)
    {
        $this->toolbarSorting = $toolbarSorting;
    }

    /**
     * Sorting collection by custom options
     *
     * @see   \Magento\Catalog\Block\Product\ProductList\Toolbar::setCollection()
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     * @param callable $proceed
     * @param $collection
     * @return mixed
     */
    public function aroundSetCollection(
        OriginalClass $subject,
        callable $proceed,
        $collection
    ) {
        $currentOrder = $subject->getCurrentOrder();

        if ($currentOrder) {
            $options = $this->toolbarSorting->getSortByOptionsArr();

            if (isset($options[$currentOrder])) {
                $collection = $this->toolbarSorting->processProductCollection(
                    $currentOrder,
                    $collection
                );
            }
        }

        return $proceed($collection);
    }
}
