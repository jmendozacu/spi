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

namespace BinaryAnvil\Toolbar\Model\Sorting\Handler;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Api\SortOrder as ApiSortOrder;
use BinaryAnvil\Toolbar\Model\Sorting\HandlerInterface;
use BinaryAnvil\Toolbar\Model\Config\Source\SortBy as SortBySource;

class SortOrder implements HandlerInterface
{
    /**
     * Mapping option <=> SortOrder (ASC|DESC)
     *
     * @var array
     */
    private $sortOrderOptMapping = [
        SortBySource::SORT_OPTION_CODE_CREATED_AT_ASC       => ApiSortOrder::SORT_ASC,
        SortBySource::SORT_OPTION_CODE_PRICE_ASC            => ApiSortOrder::SORT_ASC,
        SortBySource::SORT_OPTION_CODE_PRICE_DESC           => ApiSortOrder::SORT_DESC,
        SortBySource::SORT_OPTION_CODE_CREATED_AT_DESC      => ApiSortOrder::SORT_DESC,
        SortBySource::SORT_OPTION_CODE_ON_SALE              => ApiSortOrder::SORT_DESC
    ];

    /**
     * Custom sorting option <=> product attribute
     * (for collection filtering)
     *
     * @var array
     */
    private $attrOptMapping = [
        SortBySource::SORT_OPTION_CODE_PRICE_ASC            => ProductInterface::PRICE,
        SortBySource::SORT_OPTION_CODE_PRICE_DESC           => ProductInterface::PRICE,
        SortBySource::SORT_OPTION_CODE_CREATED_AT_ASC       => ProductInterface::CREATED_AT,
        SortBySource::SORT_OPTION_CODE_CREATED_AT_DESC      => ProductInterface::CREATED_AT,
        SortBySource::SORT_OPTION_CODE_ON_SALE              => SortBySource::ORIGIN_SORT_OPTION_CODE_ON_SALE
    ];

    /**
     * @inheritdoc
     */
    public function process($sortOptionCode, $productCollection)
    {
        return $productCollection->setOrder(
            $this->attrOptMapping[$sortOptionCode],
            $this->sortOrderOptMapping[$sortOptionCode]
        );
    }
}
