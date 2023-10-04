define([
    'jquery',
    'Magento_Checkout/js/view/summary/abstract-total'
], function ($, Component) {
    "use strict";

    return Component.extend({
        defaults: {
            template: 'Riverstone_RewardPoints/checkout/summary/reward-points'
        },
    });
});
