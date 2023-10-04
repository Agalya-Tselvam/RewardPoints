define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/model/cart/totals-processor/default',
        'Magento_Catalog/js/price-utils',
    ],
    function (
        $,
        ko,
        Component,
        quote,
        totals,
        defaultTotal,
        priceUtils
    ) {
        'use strict';
        console.log('hello');

        return Component.extend({
            defaults: {
                template: 'Riverstone_RewardPoints/checkout/rewardpoints'
            },
            initialize: function () {
                this.isApplied = ko.observable(false);
                this.discountAmount = ko.observable(0);

                this._super();
            },
            /**
             * Apply action
             */
            apply: function() {
                console.log('hello');
            },
            /**
             * Cancel action
             */
            cancel: function() {
            },
            /**
             * Form validation
             *
             * @returns {boolean}
             */
            validate: function() {
                var form = '#discount-form';
                return $(form).validation() && $(form).validation('isValid');
            },
        });
    }
);
