<?php

namespace BinaryAnvil\Toolbar\Model\Config\Source;

use BinaryAnvil\Toolbar\Model\Config\SourceAbstract;

/**
 * Class SortBy
 * @package BinaryAnvil\Toolbar\Model\Config\Source
 */
class SortBy extends SourceAbstract
{


    /**
     * Custom sorting options codes
     */
    const SORT_OPTION_CODE_PRICE_ASC = 'price_asc';
    const SORT_OPTION_CODE_PRICE_DESC = 'price_desc';
    const SORT_OPTION_CODE_ON_SALE = 'on_sale';
    const SORT_OPTION_CODE_CREATED_AT_ASC = 'created_at_asc';
    const SORT_OPTION_CODE_CREATED_AT_DESC = 'created_at_desc';
    const SORT_OPTION_CODE_BEST_SELLING = 'best_selling';
    const SORT_OPTION_CODE_TOP_RATE = 'top_rated';


    /**
     * origin magento attribute for sort by on sale
     * @type string
     */
    const ORIGIN_SORT_OPTION_CODE_ON_SALE = 'custom_on_sale';
    /**#@- */


    /**
     * @return array|mixed
     */
    public function toArray()
    {
        return [
            self::SORT_OPTION_CODE_PRICE_ASC => __('Lowest Price'),
            self::SORT_OPTION_CODE_PRICE_DESC => __('Highest Price'),
            self::SORT_OPTION_CODE_CREATED_AT_DESC => __('Newest'),
            self::SORT_OPTION_CODE_CREATED_AT_ASC => __('Oldest'),
            self::SORT_OPTION_CODE_BEST_SELLING => __('Best Selling'),
            self::SORT_OPTION_CODE_TOP_RATE => __('Top Rated'),
            self::SORT_OPTION_CODE_ON_SALE => __('On Sale'),
        ];
    }
}
