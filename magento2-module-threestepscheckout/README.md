# MB_ThreeStepsCheckout Magento 2 module

## Overview
This module adds "Finalization" step at the end of standard Magento 2 checkout.

## Configuration
The module requires that "Configuration > Sales > Checkout > Checkout Options > Display Billing Address On" config is set to "Payment Page".

The module itself has no configuration. If module is enabled, checkout is overwritten.

## Payment methods coverage
Due to the fact that "Place order" button is hardcoded in each payment method's template, there is a need to move any additional validation attached to the button to module's "Next" button.

Below you can find a list of payment methods that either don't have additional validations or for which validation was already moved. They were tested with module with success.

* Magento_OfflinePayments: Bank transfer
* Magento_OfflinePayments: Checkmo
* PayPal_Braintree: Credit Card
* PayPal_Braintree: Credit Card Vault

If you need to use other methods, you must test them by your own and move additional validation if needed.

## Magento versions supported

* 2.3.x
* 2.4.x