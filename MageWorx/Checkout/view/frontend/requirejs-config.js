var config = {
    config: {
        mixins: {
            // Specific payment methods mixins
            'PayPal_Braintree/js/view/payment/method-renderer/paypal': {
                'MageWorx_Checkout/js/view/payment/method-renderer/new-braintree-paypal-method-mixin': true
            },
            'Magento_Paypal/js/view/payment/method-renderer/in-context/checkout-express': {
                'MageWorx_Checkout/js/view/payment/method-renderer/paypal-express-in-context-method-mixin': true
            },
            // Fix region custom entry additionalClasses property
            'Magento_Ui/js/form/element/region': {
                'MageWorx_Checkout/js/view/form/element/region-mixin': true
            },
            // Fix agreements assigner (fix agreements validation)
            'Magento_CheckoutAgreements/js/model/agreements-assigner': {
                'MageWorx_Checkout/js/model/agreements-assigner-mixin': true
            },
            // Fix customer email validation
            'Magento_Checkout/js/model/customer-email-validator': {
                'MageWorx_Checkout/js/model/customer-email-validation-mixin': true
            },
            // Fix agreements validation
            'Magento_CheckoutAgreements/js/model/agreement-validator': {
                'MageWorx_Checkout/js/model/agreement-validator-mixin': true
            },
            // Fix empty shipping address when it is not set in the customers account
            'Magento_Checkout/js/model/checkout-data-resolver': {
                'MageWorx_Checkout/js/model/checkout-data-resolver-mixin': true
            },
            'Magento_Checkout/js/action/select-payment-method': {
                'MageWorx_Checkout/js/action/select-payment-method-mixin': true
            },
            'Magento_Checkout/js/model/new-customer-address': {
                'MageWorx_Checkout/js/model/new-customer-address-mixin': true
            },
            'Magento_Ui/js/block-loader': {
                'MageWorx_Checkout/js/view/loaders/block-loader-mixin': true
            },
            'Magento_Checkout/js/model/shipping-rates-validation-rules': {
                'MageWorx_Checkout/js/model/shipping-rates-validation-rules-mixin': true
            },
            // Add our item details mixin
            'Magento_Checkout/js/view/summary/item/details': {
                'MageWorx_Checkout/js/view/summary/item/details-mixin': true
            }
        }
    },
    paths: {
        'popper': 'MageWorx_Checkout/js/bootstrap/popper.min',
        'mage/loader': 'MageWorx_Checkout/js/view/loaders/loader'
    },
    shim: {
        'popper': {
            'deps': ['jquery'],
            'exports': 'Popper'
        }
    },
    map: {
        '*': {
            'Magento_Checkout/js/action/select-billing-address':'MageWorx_Checkout/js/action/select-billing-address'
        }
    }
}
