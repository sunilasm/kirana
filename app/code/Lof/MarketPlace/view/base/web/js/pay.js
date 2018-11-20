define([
    //'uiComponent',
    'Magento_Ui/js/grid/columns/multiselect',
    'Magento_Catalog/js/price-utils'
], function (Component, utils, paging) {
    'use strict';
    return Component.extend({
        initialize: function () {
            this._super();
            this.totalSellerPrice = this.totalSelected;
        },
        updateState: function () {
            this.totalSellerPrice = this.totalSelected;
        }
    });
});