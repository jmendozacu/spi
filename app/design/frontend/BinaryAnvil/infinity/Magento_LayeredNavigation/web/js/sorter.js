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
        'jquery'
    ],
    function ($) {
        return function (config, element) {
            var $sortByFilterContent = $(config.sortByFilterContent),
                $sorter = $(config.sorter),
                $sorterOptions = $(config.sorterOptions),
                $accordionFilters = $(config.accordionFilters),
                $accordionSorter = $(config.accordionSorter),
                $accordionCategory = $(config.accordionCategory),
                $filterOptionsTitle = $(config.filterOptionsTitle),
                $filterOptionsItem = $(config.filterOptionsItem),
                accountInfoBlock = config.accountInfoBlock,
                closeButton = config.closeButton,
                $layeredFilters = $(config.layeredFilters),
                dropDownActiveClass = 'drop-down-active',
                mobileFiltersTitle = config.mobileFiltersTitle,
                $categoryFilterItem = $(config.categoryFilterItem);

            if ($accordionFilters.children().length === 0) {
                $(mobileFiltersTitle).addClass('disabled');
            }

            function accordionFiltersDeactivate() {
                $accordionFilters.children(config.filterOptionsTitle).each(function(i) {
                    if($(this).attr('aria-expanded') === 'true') {
                        $accordionFilters.accordion('deactivate', i);
                    }
                });
            };

            $sorterOptions.each(function() {
                $sortByFilterContent.append('<p ' + 'data-sort-value=\"' + this.value +'\">' + this.text +'</p>');
            });

            $sortByFilterContent.on('click', 'p', function(){
                var sortValue = $(this).attr('data-sort-value');
                $sorter.val(sortValue).trigger('change');
            });

            $filterOptionsTitle.on('click', function() {
                if($(this).closest(config.accordionSorter).length !==0 ) {
                    accordionFiltersDeactivate();
                    $accordionCategory.accordion('deactivate', 0);
                } else {
                    if ($accordionSorter.children(config.filterOptionsTitle).attr('aria-expanded') === 'true') {
                        $accordionSorter.accordion('deactivate', 0);
                    }
                }
            });

            $categoryFilterItem.on('click', function() {
                var self = this;

                if ($(self).attr('disabled')) {
                    return false;
                } else {
                    location.href = $(self).find('a').attr('href');
                }

                $categoryFilterItem.each(function () {
                    if (this !== self) {
                        $(this).removeClass('active').attr('disabled', 'disabled');
                    }
                });

                $(self).toggleClass('active').attr('disabled', 'disabled');
            });

            $(document).on('click', function (e) {
                if (($accordionSorter.children('.active').length !== 0 &&
                        $(e.target).closest('#layered-filter-block').length === 0) ||
                    $(e.target).hasClass(closeButton)) {
                    $accordionSorter.accordion('deactivate', 0);
                }

                if ($accordionFilters.children().attr('.active') !== 0 &&
                    $(e.target).closest('#layered-filter-block').length === 0) {
                    accordionFiltersDeactivate();
                }

                if (($accordionCategory.children().attr('.active') !== 0 &&
                        $(e.target).closest('#layered-filter-block').length === 0) ||
                    $(e.target).hasClass(closeButton)) {
                    $accordionCategory.accordion('deactivate', 0);
                }

                if ($(e.target).closest(config.accordionCategory).length !== 0 &&
                    $accordionCategory.children('.active').length !== 0) {
                    $layeredFilters.addClass(dropDownActiveClass);
                } else if ($(e.target).closest(config.accordionSorter).length !== 0 &&
                    $accordionSorter.children('.active').length !== 0) {
                    $layeredFilters.addClass(dropDownActiveClass);
                } else {
                    $layeredFilters.removeClass(dropDownActiveClass);
                    $accordionCategory.accordion('deactivate', 0);
                    $accordionSorter.accordion('deactivate', 0);
                }

                if ($(e.target).closest(mobileFiltersTitle).length !== 0) {
                    $accordionCategory.accordion('deactivate', 0);
                    $accordionSorter.accordion('deactivate', 0);
                }
            });

            var observeObject = function () {
                var _class = {
                    init: function (selector, callback) {
                        var element = document.querySelector(selector);

                        try {
                            var observer = new MutationObserver(function (mutations) {
                                mutations.forEach(function (mutation) {
                                    callback(mutation.target, mutation.attributeName, mutation.oldValue);
                                });
                            });

                            observer.observe(element, {
                                attributes: true,
                                subtree: true,
                                attributeOldValue: true
                            });
                        } catch (z) {
                            element.addEventListener('DOMAttrModified', function (e) {
                                callback(e.target, e.attrName, e.prevValue);
                            }, false);
                        }
                    }
                };

                return _class;
            }();

            $(function () {
                observeObject.init(accountInfoBlock, function (target, name, oldValue) {
                    if ($(target).hasClass('active') && $filterOptionsItem.hasClass('active')) {
                        accordionFiltersDeactivate();
                        $accordionSorter.accordion('deactivate', 0);
                        $accordionCategory.accordion('deactivate', 0);
                    }
                });
            });
        }
    });
