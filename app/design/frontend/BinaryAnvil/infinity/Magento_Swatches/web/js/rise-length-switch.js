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
 * @package     Infinity
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

define([
    'jquery',
    'matchMedia'
], function ($, mediaCheck) {
    'use strict';

    var $swatchOpt = $(".swatch-opt"),
        swatchAttrRiseOptions = '.is_apparel_rise_length .swatch-attribute-options',
        swatchRiseItem = '.is_apparel_rise_length .swatch-option.text',
        swatchAttrRiseLabel = ".is_apparel_rise_length .swatch-attribute-label",

        swatchAttrFootwearOptions = '.is_footwear_width .swatch-attribute-options',
        swatchFootwearItem = '.is_footwear_width .swatch-option.text',
        swatchAttrFootwearLabel = ".is_footwear_width .swatch-attribute-label";

    mediaCheck({
        media: '(max-width: 767px)',

        /**
         * Switch to Mobile Version.
         */
        entry: function () {
            function selectOption(el, swLabel, swOptions) {
                $(el).closest($swatchOpt).find(swLabel).text($(el).text());
                $(el).closest($swatchOpt).find(swOptions).slideUp().removeClass('expanded');
                $(swLabel).removeClass('expanded');
            }

            function slideSwatch (el, swLabel, swOptions) {
                if($(swLabel).hasClass('expanded')) {
                    $(swOptions).slideUp().removeClass('expanded');
                    $(swLabel).removeClass('expanded');
                }
                else {
                    $(swOptions).slideDown().addClass('expanded');
                    $(swLabel).addClass('expanded');
                }
            }

            $swatchOpt.on('click', swatchAttrRiseLabel, function(e) {
                e.preventDefault();
                slideSwatch(e, swatchAttrRiseLabel, swatchAttrRiseOptions);
            });

            $swatchOpt.on('click', swatchAttrFootwearLabel, function(e) {
                e.preventDefault();
                slideSwatch(e, swatchAttrFootwearLabel, swatchAttrFootwearOptions);
            });

            $swatchOpt.on('click', swatchRiseItem, function (e) {
                selectOption(e.target, swatchAttrRiseLabel, swatchAttrRiseOptions);
            });

            $swatchOpt.on('click', swatchFootwearItem, function (e) {
                selectOption(e.target, swatchAttrFootwearLabel, swatchAttrFootwearOptions);
            });

            if($(swatchRiseItem).hasClass('selected')) {
                $(swatchAttrRiseLabel).text($(swatchRiseItem+'.selected').text());
            }

            if($(swatchFootwearItem).hasClass('selected')) {
                $(swatchAttrFootwearLabel).text($(swatchFootwearItem+'.selected').text());
            }
        },

        /**
         * Switch to Desktop Version.
         */
        exit: function () {
            $(swatchAttrRiseOptions).removeClass('expanded').css('display', '');
            $swatchOpt.off('click', swatchRiseItem);
            $(swatchAttrRiseLabel).removeClass('expanded');
            $swatchOpt.off('click', swatchAttrRiseLabel);

            $(swatchAttrFootwearOptions).removeClass('expanded').css('display', '');
            $swatchOpt.off('click', swatchFootwearItem);
            $(swatchAttrFootwearLabel).removeClass('expanded');
            $swatchOpt.off('click', swatchAttrFootwearLabel);
        }
    });
});