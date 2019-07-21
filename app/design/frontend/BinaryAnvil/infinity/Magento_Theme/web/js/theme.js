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
    'mage/smart-keyboard-handler',
    'mage/mage',
    'mage/ie-class-fixer',
    'domReady!'
], function ($, keyboardHandler) {
    'use strict';

    if ($('body').hasClass('checkout-cart-index')) {
        if ($('#co-shipping-method-form .fieldset.rates').length > 0 &&
            $('#co-shipping-method-form .fieldset.rates :checked').length === 0
        ) {
            $('#block-shipping').on('collapsiblecreate', function () {
                $('#block-shipping').collapsible('forceActivate');
            });
        }
    }

    $('.panel.header > .header.links').clone().appendTo('#store\\.links');

    keyboardHandler.apply();

    if ($('body').hasClass('cms-home') || $('body').hasClass('cms-lookbook')) {
        function newsClick() {
            jQuery('.header-newsletter a')[0].click();
        }

        $(document).ready(function () {

            // Cookie Functions
            function readCookie(name) {
                var nameEQ = name + "=";
                var ca = document.cookie.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') {
                        c = c.substring(1, c.length);
                    }
                    if (c.indexOf(nameEQ) == 0) {
                        return c.substring(nameEQ.length, c.length);
                    }
                }
            }
            function createCookie(name,value,days) {
                if (days) {
                    var date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 *1000));
                    var expires = "; expires=" + date.toGMTString();
                } else {
                    var expires = "";
                }
                document.cookie = name + "=" + value + expires + "; path=/";
            }

            // mp4 video
            var video = document.getElementById('home-video'),
                windowSize = $(window).width(),
                mobileVideoSrc = 'https://i.infinityscrubs.com/infinity/videos/INFScrubs_HomepageVideo_mobile.mp4';

            $('#home-video, .play-btn').on('ended', function() {
                video.autoplay=false;
                video.load();
                $('.play-btn').fadeIn();
            });

            if (windowSize < 768) {
                // $('#home-video source').attr('src', mobileVideoSrc);

                $('#home-video, .play-btn').on('touchstart', function() {
                    if (video.paused === true) {
                        $('.play-btn').fadeOut();
                        video.play();
                        if (video.muted){
                            video.muted = false;
                        } else{
                            video.muted = true;
                        }
                    } else {
                        $('.play-btn').fadeIn();
                        video.pause();
                    }
                });
            } else {
                $('#home-video, .play-btn').on('click', function() {
                    if (video.paused) {
                        $('.play-btn').fadeOut();
                        video.play();

                        if (video.muted){
                            video.muted = false;
                        } else{
                            video.muted = true;
                        }
                    } else {
                        $('.play-btn').fadeIn();
                        video.pause();
                    }
                });
            }

            // Checking for Newsletter Cookie
            var is_safari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);

            if (document.documentMode || /Edge/.test(navigator.userAgent) || is_safari) {
                // console.log(document.cookie.split(';'));

                if (!readCookie('newsletter_viewed')) {

                    var now = new Date();
                    var time = now.getTime();
                    time += 3600 * 1000;
                    now.setTime(time);

                    setTimeout(function() {
                        newsClick();
                    }, 500);

                    setTimeout(function() {
                        createCookie('newsletter_viewed', 'true', now.toUTCString());
                    }, 1000)
                }

            } else {
                if (!readCookie('newsletter_viewed')) {

                    setTimeout(function() {
                        $('.header-newsletter a').click();

                        createCookie('newsletter_viewed', 'true')
                    }, 800);

                }
            }

            /*
            * rwdImageMaps jQuery plugin v1.6
            * Licensed under the MIT license
            */
            jQuery(function() {
                ;(function(a){a.fn.rwdImageMaps=function(){var c=this;var b=function(){c.each(function(){if(typeof(a(this).attr("usemap"))=="undefined"){return}var e=this,d=a(e);a("<img />").on('load',function(){var g="width",m="height",n=d.attr(g),j=d.attr(m);if(!n||!j){var o=new Image();o.src=d.attr("src");if(!n){n=o.width}if(!j){j=o.height}}var f=d.width()/100,k=d.height()/100,i=d.attr("usemap").replace("#",""),l="coords";a('map[name="'+i+'"]').find("area").each(function(){var r=a(this);if(!r.data(l)){r.data(l,r.attr(l))}var q=r.data(l).split(","),p=new Array(q.length);for(var h=0;h<p.length;++h){if(h%2===0){p[h]=parseInt(((q[h]/n)*100)*f)}else{p[h]=parseInt(((q[h]/j)*100)*k)}}r.attr(l,p.toString())})}).attr("src",d.attr("src"))})};a(window).resize(b).trigger("resize");return this}})(jQuery);

                jQuery('img[usemap]').rwdImageMaps();
            });
        });
    }

    // Lookbook Page Specific code here
    if (document.querySelector('body').classList[0] === 'cms-lookbook') {
        if (window.innerWidth < 992) {
            const footwearImg = document.querySelector('.footwear-row .flex-60');
            const footwearImg2 = document.querySelector('.footwear-row .flex-40');

            footwearImg.parentNode.insertBefore(footwearImg, footwearImg2);
        }
    }
});
