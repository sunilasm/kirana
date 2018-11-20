/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            "fastclick":            'Lof_MarketPlace/js/fastclick',
            "translateInline":      "mage/translate-inline",
            "form":                 "mage/backend/form",
            "button":               "mage/backend/button",
            "accordion":            "mage/accordion",
            "actionLink":           "mage/backend/action-link",
            "validation":           "mage/backend/validation",
            "notification":         "mage/backend/notification",
            "loader":               "mage/loader_old",
            "loaderAjax":           "mage/loader_old",
            "floatingHeader":       "mage/backend/floating-header",
            "suggest":              "mage/backend/suggest",
            "mediabrowser":         "jquery/jstree/jquery.jstree",
            "tabs":                 "mage/backend/tabs",
            "treeSuggest":          "mage/backend/tree-suggest",
            "calendar":             "mage/calendar",
            "dropdown":             "mage/dropdown_old",
            "collapsible":          "mage/collapsible",
            "jstree":               "jquery/jstree/jquery.jstree",
            "details":              "jquery/jquery.details",
            "validate":             "jquery/jquery.validate",
            'icheck':               'Lof_MarketPlace/js/icheck',
            'dataTables-bootstrap': 'Lof_MarketPlace/js/dataTables.bootstrap',
            'raphael':          "Lof_MarketPlace/js/raphael",
            'morris':          "Lof_MarketPlace/js/morris",
            categoryForm:       'Lof_MarketPlace/catalog/category/form',
            newCategoryDialog:  'Lof_MarketPlace/js/new-category-dialog',
            categoryTree:       'Lof_MarketPlace/js/category-tree',
            productGallery:     'Lof_MarketPlace/js/product-gallery',
            baseImage:          'Lof_MarketPlace/catalog/base-image-uploader',
            newVideoDialog:     'Lof_MarketPlace/js/video/new-video-dialog',
            openVideoModal:     'Lof_MarketPlace/js/video/video-modal',
            productAttributes:  'Lof_MarketPlace/catalog/product-attributes',
            menu:               'mage/backend/menu',
           
        }
    },
    "shim": {
        "jquery/bootstrap": ["jquery","jquery/ui"],
        "custom": ["jquery","jquery/bootstrap"],
        "jquery/custom": ["jquery","jquery/bootstrap"],
        "jquery/slimscroll": ["jquery"],
        "jquery/dataTables": ["jquery"],
        "jquery/vmap": ["jquery"],
        "jquery/vmap.world": ["jquery"],
        "jquery/vmap.sampledata": ["jquery"],
        "jquery/fix_prototype_bootstrap": ["jquery","jquery/bootstrap","prototype"],
        "productGallery": ["jquery/fix_prototype_bootstrap"],
        "Lof_MarketPlace/catalog/apply-to-type-switcher": ["Lof_MarketPlace/catalog/type-events"],
        // 'jquery/blueimp_gallery': ["jquery","prototype"]
    },
    "deps": [
         "mage/backend/bootstrap",
         "mage/adminhtml/globals",
         "fastclick",
         // 'jquery/blueimp_gallery',
         "jquery/bootstrap",
         'icheck',
         "jquery/slimscroll",
        "jquery/dataTables",
         // "jquery/vmap",
         // "jquery/vmap.world",
         // "jquery/vmap.sampledata",
         'dataTables-bootstrap',
         'raphael',
         'morris',
         "jquery/custom",
         "jquery/fix_prototype_bootstrap",
         // 'load_image',
         /*'canvas_to_blob',*/

         ],
    "paths": {
        /*'prototype': 'prototype/prototype',*/
        "jquery/ui": "jquery/jquery-ui-1.9.2",
        // 'jquery/blueimp_gallery':    "Lof_MarketPlace/js/jquery.blueimp-gallery",
        "jquery/bootstrap": "Lof_MarketPlace/js/bootstrap",
        "jquery/slimscroll": 'Lof_MarketPlace/js/jquery.slimscroll',
        "jquery/dataTables": 'Lof_MarketPlace/js/jquery.dataTables',
        // "jquery/vmap": 'Lof_MarketPlace/js/jquery.vmap',
        // "jquery/vmap.world": 'Lof_MarketPlace/js/jquery.vmap.world',
        // "jquery/vmap.sampledata": 'Lof_MarketPlace/js/jquery.vmap.sampledata',
        "jquery/custom": "Lof_MarketPlace/js/custom",
        "jquery/fix_prototype_bootstrap": "Lof_MarketPlace/js/fix_prototype_bootstrap",
        "Magento_Catalog/catalog/type-events": "Lof_MarketPlace/catalog/type-events",
        "Magento_Catalog/catalog/apply-to-type-switcher": "Lof_MarketPlace/catalog/apply-to-type-switcher",
        "Magento_Catalog/js/product/weight-handler":"Lof_MarketPlace/js/product/weight-handler",
        "Magento_Catalog/js/product-gallery":"Lof_MarketPlace/js/product-gallery",
        "Magento_ProductVideo/js/get-video-information":"Lof_MarketPlace/js/video/get-video-information"
    }
};