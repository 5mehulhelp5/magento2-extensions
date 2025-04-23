<?php

/**
 * Copyright © Michał Biarda. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MB\ThreeStepsCheckout\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Checkout\Helper\Data;
use Magento\Framework\Exception\ConfigurationMismatchException;

class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var Data
     */
    private $checkoutDataHelper;

    public function __construct(Data $checkoutDataHelper)
    {
        $this->checkoutDataHelper = $checkoutDataHelper;
    }

    public function process($jsLayout): array
    {
        $jsLayout = $this->customizeSharedBillingAddress($jsLayout);
        $jsLayout = $this->addBillingInfoToSidebar($jsLayout);
        $jsLayout = $this->moveCheckoutAgreementsToFinalizationStep($jsLayout);
        return $jsLayout;
    }

    private function customizeSharedBillingAddress(array $jsLayout): array
    {
        $paymentLayout = $this->getPaymentLayout($jsLayout);
        if ($this->checkoutDataHelper->isDisplayBillingOnPaymentMethodAvailable()) {
            throw new ConfigurationMismatchException(
                __('Three Steps Checkout module requires shareable billing address. Please set "Configuration > Sales > Checkout > Checkout Options > Display Billing Address On" to "Payment Page".')
            );
        } else {
            $paymentLayout = $this->changeBillingAddressTemplate($paymentLayout);
            $paymentLayout = $this->moveBillingAddressToTop($paymentLayout);
            $paymentLayout = $this->addContinueButton($paymentLayout);
            $jsLayout = $this->updatePaymentLayout($jsLayout, $paymentLayout);
        }
        return $jsLayout;
    }

    private function getPaymentLayout(array $jsLayout): array
    {
        $paymentLayout = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
        ['children']['payment']['children'];
        return $paymentLayout;
    }

    private function changeBillingAddressTemplate(array $paymentLayout): array
    {
        $paymentLayout['afterMethods']['children']['billing-address-form']['config']['template']
            = 'MB_ThreeStepsCheckout/billing-address.html';
        return $paymentLayout;
    }

    private function moveBillingAddressToTop(array $paymentLayout): array
    {
        $paymentLayout['beforeMethods']['children']['billing-address-form']
            = $paymentLayout['afterMethods']['children']['billing-address-form'];
        unset($paymentLayout['afterMethods']['children']['billing-address-form']);
        return $paymentLayout;
    }

    private function addContinueButton(array $paymentLayout): array
    {
        $paymentLayout['afterMethods']['children']['next'] = [
            'sortOrder' => 1000,
            'component' => 'MB_ThreeStepsCheckout/js/view/payment/next',
            'template' => 'MB_ThreeStepsCheckout/payment/next'
        ];
        return $paymentLayout;
    }

    private function updatePaymentLayout(array $jsLayout, array $paymentLayout): array
    {
        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
        ['payment']['children'] = $paymentLayout;
        return $jsLayout;
    }

    private function addBillingInfoToSidebar(array $jsLayout): array
    {
        $jsLayout['components']['checkout']['children']['sidebar']['children']['billing-information'] = [
            'component' => 'MB_ThreeStepsCheckout/js/view/billing-information',
            'displayArea' => 'summary',
            'children' => [
                'bill-to' => [
                    'component' => 'MB_ThreeStepsCheckout/js/view/billing-information/details',
                    'displayArea' => 'bill-to',
                    'provider' => 'checkoutProvider',
                    'deps' => 'checkoutProvider'
                ]
            ]
        ];
        return $jsLayout;
    }

    private function moveCheckoutAgreementsToFinalizationStep(array $jsLayout): array
    {
        $agreements = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
        ['payment']['children']['payments-list']['children']['before-place-order']['children']['agreements'];
        unset(
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['payments-list']['children']['before-place-order']['children']['agreements']
        );
        $jsLayout['components']['checkout']['children']['steps']['children']['finalization']['children']['agreements']
            = $agreements;
        return $jsLayout;
    }
}
