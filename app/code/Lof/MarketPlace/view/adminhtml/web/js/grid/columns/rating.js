/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'underscore',
    'Magento_Ui/js/grid/columns/column'
], function (_, Column) {
    'use strict';

    return Column.extend({
    	defaults: {
            bodyTmpl: 'ui/grid/cells/html'
    	},
        /*eslint-disable eqeqeq*/
        /**
         * Retrieves label associated with a provided value.
         *
         * @returns {String}
         */
        getLabel: function (record) {
            console.log(record);
        	var value = record[this.index];
        	var percent = value / 5 * 100;
        	return '<div id="summary-rating" class="field-summary-rating"><div class="rating-box"><div class="rating" style="width:'
        			+ percent + '%;"></div></div></div>';
        }

        /*eslint-enable eqeqeq*/
    });
});
