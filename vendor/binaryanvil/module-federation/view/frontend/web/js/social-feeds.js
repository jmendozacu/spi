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
 * @package     BinaryAnvil_Federation
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

define([
    'jquery',
    'BinaryAnvil_Federation/js/action/refresh-items',
    'underscore',
    'mage/template',
    'mage/translate',
    'codebird',
    'socialfeed'
], function ($, refreshItems) {
    function main(config)
    {
        var socialSettings = {
            vk          : null,
            rss         : null,
            google      : null,
            twitter     : null,
            blogspot    : null,
            facebook    : null,
            instagram   : null,
            pinterest   : null
        };

        $.each(socialSettings, function (key) {
            if (config.hasOwnProperty(key)) {
                socialSettings[key] = config[key];
            }
        });
        
        var cacheAfterRender = function (Feed) {
            $.each(socialSettings, function (instance_key) {
                if (socialSettings[instance_key] !== null && socialSettings[instance_key].cache_expired) {
                    var itemsData = [];
                    $.each(Feed[instance_key].posts, function (key) {
                        var content = Feed[instance_key].posts[key].content;
                        if (content.hasOwnProperty('id')) {
                            content['identifier'] = content['id'];
                        }
                        if (content.hasOwnProperty('dt_create')) {
                            content['created_time'] = content['dt_create'];
                        }
                        itemsData.push(Feed[instance_key].posts[key].content);
                    });
                    refreshItems(instance_key, itemsData);
                }
            });
        };

        $(config.selector).socialfeed({
            vk          : socialSettings.vk,
            rss         : socialSettings.rss,
            google      : socialSettings.google,
            twitter     : socialSettings.twitter,
            blogspot    : socialSettings.blogspot,
            facebook    : socialSettings.facebook,
            instagram   : socialSettings.instagram,
            pinterest   : socialSettings.pinterest,

            // GENERAL SETTINGS
            length      : 200,
            show_media  : true,
            template    : config.template,
            callback    : cacheAfterRender
        });
    }

    return main;
});
