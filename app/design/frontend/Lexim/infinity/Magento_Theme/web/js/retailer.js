var isCheckOut = window.location.href.indexOf('checkout') > 0,
    isSummary = window.location.href.indexOf('onepage/success') > 0,
    isStaging = window.location.href.indexOf('mcstaging') > 0,
    infinityscrubsv3 = window.location.href.indexOf('infinityscrubsv3') > 0,
    currentUrl = window.location.href,
    cartUrl = 'https://www.infinityscrubs.com/checkout/cart/',
    checkOutUrl = 'https://www.infinityscrubs.com/checkout/';
if (infinityscrubsv3) {
    cartUrl = 'http://infinityscrubsv3.com.vn/checkout/cart/';
    checkOutUrl = 'http://infinityscrubsv3.com.vn/checkout/';
}
if (isStaging) {
    cartUrl = 'https://mcstaging.infinityscrubs.com/checkout/cart/';
    checkOutUrl = 'https://mcstaging.infinityscrubs.com/checkout/';
}


// Magento to allow jQuery
(function () {
    require(["jquery", "slick"], function ($) {

        function loadLocations(address, radius) {
            // Development and Production API URL(s) - (change for different environments)
            var urlDev = "https://storelocator-dev.azurewebsites.net/api/v1/near/address/" + address + "/radius/" + radius,
                urlProd = "https://storelocator.azurewebsites.net/api/v1/near/address/" + address + "/radius/" + radius;

            radius = radius || 10;

            jQuery.ajax({
                type: 'GET',
                url: urlProd,
                data: {},

                contentType: 'application/json; charset=utf-8',
                dataType: 'json',
                success: function (data, textStatus, jqXHR) {
                    // console.log(data);
                    var i,
                        len,
                        location,
                        html = "";

                    if (data.totalStores >= 1 && data.totalStores <= 4) {
                        jQuery('.location-carousel').slideDown();
                        jQuery('.arrow_right').hide();
                        jQuery('.arrow_left').hide();
                    } else if (data.totalStores > 4) {
                        jQuery('.location-carousel').slideDown();
                        jQuery('.arrow_right').show();
                        jQuery('.arrow_left').hide();
                    } else if (data.totalStores === 0) {
                        // jQuery(".nearest-location").html('<div class="locations-none">Sorry, no store found in this location, try searching a larger radius.</div>');
                        jQuery(".the-bottom-slider").html('<div class="locations-none">Sorry, no store found in this location. Try searching a larger radius.</div>');
                        jQuery('.location-carousel').slideUp();
                    }

                    for (i = 0, len = data.stores.length; i < len; i++) {
                        location = data.stores[i];

                        html += getLocation(location);
                    }

                    jQuery(".the-bottom-slider").html(html);

                    $('.the-bottom-slider').slick({
                        dots: false,
                        infinite: false,
                        speed: 300,
                        slidesToShow: 4,
                        slidesToScroll: 4,
                        responsive: [
                            {
                                breakpoint: 767,
                                settings: {
                                    slidesToShow: 2,
                                    slidesToScroll: 2
                                }
                            }
                        ]
                    });

                    // initLocations();

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('error');
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);

                    // jQuery(".nearest-location").html('<div class="locations-none">Sorry, no store found in this location, try searching a larger radius.</div>');
                    // jQuery('.location-carousel').slideUp();
                    jQuery(".the-bottom-slider").html('<div class="locations-none">Sorry, no store found in this location. Try searching a larger radius.</div>');

                }
            });
        }

        function getLocation(store) {
            var html = "",
                storeDistance = store.distance.toFixed(1);
            if (store.storeId) {
                html += '<div class="location '+ store.retailerId + '-' + store.storeId + '">';
            } else {
                html += '<div class="location ' + store.retailerId + '">';
            }
            html += '  <div class="location-radio">';

            html += '    <label class="label">MY LOCAL RETAILER</label>';
            html += '  </div>';
            html += '  <div class="location-body">';
            html += '<div class="name-store">';
            html += store.storeName;
            html += '  </div>';
            html += store.street1 + ", ";

            if (isset(store.street2)) {
                html += store.street2;
            }

            html += store.city + ", " + store.state + " " + store.zip + '<br />';
            if (isset(store.monday)) {
                html += 'M-F:' + store.monday + '</br>';
                html += 'Sat:' + store.saturday + '</br>';
            }
            html += store.phone + "</br>";

            // html += '    <div class="make-this-store"><input type="radio" class="radio" value="' + store.retailerId + '" name="location-option"><span>make this my store</span></div>';
            if (store.storeId) {
                html += '<div class="make-this-store"><input type="radio" class="radio retailerid-retailer" value="'+ store.retailerId + '-' + store.storeId + '" name="location-option">';
            } else {
                html += '<div class="make-this-store"><input type="radio" class="radio retailerid-retailer" value="' + store.retailerId + '" name="location-option">';
            }
            html += '    <input type="text" class="radio storename-retailer" value="' + store.storeName + '" name="storename-retailer">';
            if (isset(store.street2)) {
                html += '    <input type="text" class="radio street-retailer" value="' + store.street1 + store.street2 + '" name="street-retailer">';
            }else{
                html += '<input type="text" class="radio street-retailer" value="' + store.street1 + '" name="street-retailer">';
            }
            html += '    <input type="text" class="radio city-retailer" value="' + store.city + '" name="city-retailer">';
            html += '    <input type="text" class="radio state-retailer" value="' + store.state + '" name="state-retailer">';
            html += '    <input type="text" class="radio zip-retailer" value="' + store.zip + '" name="zip-retailer">';
            html += '    <input type="text" class="radio distance-retailer" value="' + storeDistance + '" name="distance-retailer">';
            if (isset(store.monday)) {
                html += '    <input type="text" class="radio monday-retailer" value="' + store.monday + '" name="distance-retailer">';
                html += '    <input type="text" class="radio saturday-retailer" value="' + store.saturday + '" name="distance-retailer">';
            }
            html += '    <input type="text" class="radio phone-retailer" value="' + store.phone + '" name="distance-retailer">';
            html += '    <span>make this my store</span></div>';



            html += '  </div>';
            html += '</div>';

            return html;
        }

        function createCookie(name, value, days) {
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                var expires = "; expires=" + date.toGMTString();
            } else {
                var expires = "";
            }
            document.cookie = name + "=" + value + expires + "; path=/";
        }


        function isset(str) {
            return typeof (str) !== "undefined" && str !== null && str !== "" && str !== "null" && str !== "undefined" && str !== " ";
        }

        var zipCode,
            selectedRadius,
            radiusInput = jQuery('#storeSelector'),
            finderSubmit = jQuery('#finder-submit'),
            zipInput = jQuery('.input-zip');
        // zipInput.keyup(function (e) {
        //     var inputVal = jQuery(this).val();
        //     jQuery('.the-bottom-slider .location').remove();
        //     jQuery('.the-bottom-slider .slick-arrow').remove();
        //     jQuery('.the-bottom-slider .slick-list').remove();
        //     jQuery('.the-bottom-slider').removeClass('slick-initialized');
        //     jQuery('.the-bottom-slider').removeClass('slick-slider');

        //     // If zip code is 5 characters and is numeric value gather results
        //     if (inputVal.length === 5 && $.isNumeric(inputVal)) {
        //         var radius = radiusInput.val();
        //         zipCode = jQuery(this).val();

        //         loadLocations(zipCode, radius);
        //     }

        // });

        // radiusInput.on('change', function () {
        //     jQuery('.the-bottom-slider .location').remove();
        //     jQuery('.the-bottom-slider .slick-arrow').remove();
        //     jQuery('.the-bottom-slider .slick-list').remove();
        //     jQuery('.the-bottom-slider').removeClass('slick-initialized');
        //     jQuery('.the-bottom-slider').removeClass('slick-slider');
        //     var selectedRadius = jQuery(this).val();

        //     zipCode = jQuery('.input-zip').val();

        //     loadLocations(zipCode, selectedRadius);
        // });

        finderSubmit.on('click', function () {
            jQuery('.the-bottom-slider .location').remove();
            jQuery('.the-bottom-slider .slick-arrow').remove();
            jQuery('.the-bottom-slider .slick-list').remove();
            jQuery('.the-bottom-slider').removeClass('slick-initialized');
            jQuery('.the-bottom-slider').removeClass('slick-slider');

            zipCode = zipInput.val();
            selectedRadius = radiusInput.val();
            loadLocations(zipCode, selectedRadius);
        });

    });
})();


