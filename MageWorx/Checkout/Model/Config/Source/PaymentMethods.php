<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class PaymentMethods implements OptionSourceInterface
{
    /**
     * @var \Magento\Payment\Helper\Data
     */
    private $paymentHelper;

    /**
     * PaymentMethods constructor.
     *
     * @param \Magento\Payment\Helper\Data $paymentHelper
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentHelper
    ) {
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $methods[] = [
            'label' => __('Empty'),
            'value' => ''
        ];

        $paymentMethods = $this->paymentHelper->getPaymentMethodList(
            true,
            true,
            true
        );

        $methods += $paymentMethods;

        return $methods;
    }
}
