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
 * @package     default
 * @copyright   Copyright (c) 2017-2018 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

var config = {
    paths: {
        'owlcarousel': "Magento_Catalog/js/owl.carousel.min",
        'matchHeight': "Magento_Catalog/js/jquery.matchHeight.min",
        'qtyHandlerCatalog': "Magento_Catalog/js/qty.handlerCatalog"
    },
    shim: {
        'owlcarousel': {
            deps: ['jquery']
        },
        'matchHeight': {
            deps: ['jquery']
        }
    }
};
