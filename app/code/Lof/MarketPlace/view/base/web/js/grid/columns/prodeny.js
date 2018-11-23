define([
    './column',
    'jquery',
    'mage/template',
    'text!Lof_MarketPlace/templates/grid/cells/deny/product.html',
    'Magento_Ui/js/modal/modal'
], function (Column, $, mageTemplate, denyPreviewTemplate) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/html',
            fieldClass: {
                'data-grid-html-cell': true
            }
        },
        gethtml: function (row) {
            return row[this.index + '_html'];
        },
        getFormaction: function (row) {
            return row[this.index + '_formaction'];
        },
        getProductid: function (row) {
            return row[this.index + '_productid'];
        },
        getSellerid: function (row) {
            return row[this.index + '_sellerid'];
        },
        getEntityid: function (row) {
            return row[this.index + '_entityid'];
        },
        getLabel: function (row) {
            return row[this.index + '_html']
        },
        getTitle: function (row) {
            return row[this.index + '_title']
        },
        getSubmitlabel: function (row) {
            return row[this.index + '_submitlabel']
        },
        getCancellabel: function (row) {
            return row[this.index + '_cancellabel']
        },
        preview: function (row) {
            var modalHtml = mageTemplate(
                denyPreviewTemplate,
                {
                    html: this.gethtml(row),
                    title: this.getTitle(row),
                    label: this.getLabel(row),
                    formaction: this.getFormaction(row),
                    productid: this.getProductid(row),
                    sellerid: this.getSellerid(row),
                    entityid: this.getEntityid(row),
                    submitlabel: this.getSubmitlabel(row),
                    cancellabel: this.getCancellabel(row),
                    linkText: $.mage.__('Go to Details Page')
                }
            );
            var previewPopup = $('<div/>').html(modalHtml);
            previewPopup.modal({
                title: this.getTitle(row),
                innerScroll: true,
                modalClass: '_image-box',
                buttons: []}).trigger('openModal');
        },
        getFieldHandler: function (row) {
            return this.preview.bind(this, row);
        }
    });
});
