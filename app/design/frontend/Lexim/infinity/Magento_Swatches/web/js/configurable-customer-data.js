/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'Magento_ConfigurableProduct/js/options-updater',
    'jquery/ui'
], function ($, Updater) {
    'use strict';

    $.widget('mage.selectSwatch', {
        options: {
            swatchOptions: null,
            selectors: {
                formSelector: '#product_addtocart_form',
                swatchSelector: '.swatch-opt'
            },
            swatchWidgetName: 'mageSwatchRenderer',
            widgetInitEvent: 'swatch.initialized',
            clickEventName: 'emulateClick'
        },

        /**
         * Widget initialisation.
         * Configurable product options updater listens to selected swatch options
         */
        _init: function () {
            var updater;

            updater = new Updater(this.options.widgetInitEvent, this.selectDefaultSwatchOptions.bind(this));
            updater.listen();
            this.listenClickOption();
        },

        /**
         * Get data and show in mobile when user click option in detail page
         */
        listenClickOption: function() {
            $('.swatch-attribute-options .swatch-option').click(function () {
                let _this = $(this);
                let label = _this.attr('option-label');
                let isColor = _this.hasClass('image');
                let code = _this.attr('data-code');
                let sizeLabel = '', fitLabel = '';

                if (isColor) { // Color
                    $('#colorPicker > p').text(label);
                } else { // Size / Fit
                    let newLabel = '';
                    if (code === 'is_size') {
                        sizeLabel = label;
                        fitLabel = $('.swatch-attribute.is_apparel_rise_length .swatch-option.selected').attr('option-label');
                    }
                    if (code === 'is_apparel_rise_length') {
                        sizeLabel = $('.swatch-attribute.is_size .swatch-option.selected').attr('option-label');
                        fitLabel = label;
                    }
                    if (sizeLabel) {
                        newLabel += sizeLabel + ' ';
                    }
                    if (sizeLabel && fitLabel) {
                        newLabel += '/ '
                    }
                    if (fitLabel) {
                        newLabel += fitLabel;
                    }
                    $('#sizeFitPicker > p').text(newLabel);
                    console.log(newLabel);
                }

                console.log(label);
            });
        },

        /**
         * Sets default configurable swatch attribute's selected
         */
        selectDefaultSwatchOptions: function () {
            var swatchWidget = $(this.options.selectors.swatchSelector).data(this.options.swatchWidgetName);

            if (!swatchWidget || !swatchWidget._EmulateSelectedByAttributeId) {
                return;
            }
            swatchWidget._EmulateSelectedByAttributeId(
                this.options.swatchOptions.defaultValues, this.options.clickEventName
            );
        }
    });

    return $.mage.selectSwatch;
});
