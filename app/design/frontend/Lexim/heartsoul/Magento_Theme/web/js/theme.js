/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/smart-keyboard-handler',
    'mage/mage',
    'mage/ie-class-fixer',
    'domReady!'
], function ($, keyboardHandler) {
    'use strict';

    let body = $('body');

    if (body.hasClass('checkout-cart-index')) {
        if ($('#co-shipping-method-form .fieldset.rates').length > 0 &&
            $('#co-shipping-method-form .fieldset.rates :checked').length === 0
        ) {
            $('#block-shipping').on('collapsiblecreate', function () {
                $('#block-shipping').collapsible('forceActivate');
            });
        }
    }

    $('.cart-summary').mage('sticky', {
        container: '#maincontent'
    });

    $('.panel.header > .header.links').clone().appendTo('#store\\.links');

    keyboardHandler.apply();

    // Cookie Functions
    function readCookie(name) {
        let nameEQ = name + "=";
        let ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') {
                c = c.substring(1, c.length);
            }
            if (c.indexOf(nameEQ) === 0) {
                return c.substring(nameEQ.length, c.length);
            }
        }
    }

    function createCookie(name, value, days) {
        let expires = "";
        if (days) {
            let date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toGMTString();
        }
        document.cookie = name + "=" + value + expires + "; path=/";
    }

    function newsClick() {
        jQuery('.header-newsletter a')[0].click();
    }


    $(document).ready(function () {
        let is_safari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);

        // promotion in header
        if (!readCookie('top_header_closed')) {
            $('.on-top-header').show();
        }


        // Newsletter pop up
        // if (body.hasClass('cms-home') || body.hasClass('cms-lookbook')) {
        // Checking for Newsletter Cookie
        if (document.documentMode || /Edge/.test(navigator.userAgent) || is_safari) {
            if (!readCookie('newsletter_viewed')) {
                let now = new Date();
                let time = now.getTime();
                time += 3600 * 1000;
                now.setTime(time);

                setTimeout(function () {
                    newsClick();
                }, 30000);

                setTimeout(function () {
                    createCookie('newsletter_viewed', 'true', now.toUTCString());
                }, 30000)
            }
        } else {
            if (!readCookie('newsletter_viewed')) {
                setTimeout(function () {
                    $('.header-newsletter a').click();
                    createCookie('newsletter_viewed', 'true')
                }, 30000);
            }
        }

        /*
        * rwdImageMaps jQuery plugin v1.6
        * Licensed under the MIT license
        */
        // jQuery(function() {
        //     ;(function(a){a.fn.rwdImageMaps=function(){let c=this;let b=function(){c.each(function(){if(typeof(a(this).attr("usemap"))=="undefined"){return}let e=this,d=a(e);a("<img />").on('load',function(){let g="width",m="height",n=d.attr(g),j=d.attr(m);if(!n||!j){let o=new Image();o.src=d.attr("src");if(!n){n=o.width}if(!j){j=o.height}}let f=d.width()/100,k=d.height()/100,i=d.attr("usemap").replace("#",""),l="coords";a('map[name="'+i+'"]').find("area").each(function(){let r=a(this);if(!r.data(l)){r.data(l,r.attr(l))}let q=r.data(l).split(","),p=new Array(q.length);for(let h=0;h<p.length;++h){if(h%2===0){p[h]=parseInt(((q[h]/n)*100)*f)}else{p[h]=parseInt(((q[h]/j)*100)*k)}}r.attr(l,p.toString())})}).attr("src",d.attr("src"))})};a(window).resize(b).trigger("resize");return this}})(jQuery);
        //
        //     jQuery('img[usemap]').rwdImageMaps();
        // });
        // }
    });
});
