define(
    [
    'jquery',
    'jquery/ui',
    'Magento_Search/form-mini'
    ], function ($) {
        $.widget(
            'searchanise.quickSearch', $.mage.quickSearch, {
                options: {
                    minSearchLength: 1000,
                },
            }
        );

        return $.searchanise.quickSearch;
    }
);
