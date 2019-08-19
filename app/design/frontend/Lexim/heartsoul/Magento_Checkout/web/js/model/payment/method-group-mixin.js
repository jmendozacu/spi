
define([
    'uiElement',
    'mage/translate'
], function (Element, $t) {
    'use strict';

    var mixin = {
        defaults: {
            title: $t('3. Payment')
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
