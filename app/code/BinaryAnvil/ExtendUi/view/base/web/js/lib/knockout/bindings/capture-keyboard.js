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
 * @package     BinaryAnvil_ExtendUi
 * @copyright   Copyright (c) 2017-2018 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */
define([
    'ko',
    'Magento_Ui/js/lib/knockout/template/renderer'
], function (ko, renderer) {
    'use strict';

    ko.bindingHandlers.captureKeyboard = {

        /**
         * Attaches keypress handlers to element [useCapture => true]
         * @param {HTMLElement} el - Element, that binding is applied to
         * @param {Function} valueAccessor - Function that returns value, passed to binding
         * @param  {Object} allBindings - all bindings object
         * @param  {Object} viewModel - reference to viewmodel
         */
        init: function (el, valueAccessor, allBindings, viewModel) {
            var map = valueAccessor();

            el.addEventListener('keypress', function (e) {
                var callback = map[e.keyCode];

                if (callback) {
                    return callback.call(viewModel, e);
                }
            }, true)
        }
    };

    renderer.addAttribute('captureKeyboard');
});
