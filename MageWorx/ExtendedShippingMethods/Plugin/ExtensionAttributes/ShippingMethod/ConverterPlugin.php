<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ExtendedShippingMethods\Plugin\ExtensionAttributes\ShippingMethod;

/**
 * Class ConverterPlugin
 *
 * Add data (extension attributes) to the shipping method after conversion
 *
 */
class ConverterPlugin
{
    /**
     * @var \MageWorx\ExtendedShippingMethods\Model\Processor
     */
    private $processor;

    /**
     * ConverterPlugin constructor.
     *
     * @param \MageWorx\ExtendedShippingMethods\Model\Processor $processor
     */
    public function __construct(
        \MageWorx\ExtendedShippingMethods\Model\Processor $processor
    ) {
        $this->processor = $processor;
    }

    /**
     * @param \Magento\Quote\Model\Cart\ShippingMethodConverter $subject
     * @param \Magento\Quote\Api\Data\ShippingMethodInterface $result
     * @param \Magento\Quote\Model\Quote\Address\Rate $rateModel
     * @param string $quoteCurrencyCode
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface
     */
    public function afterModelToDataObject(
        $subject,
        $result,
        $rateModel,
        $quoteCurrencyCode
    ) {
        $this->processor->process($result);

        return $result;
    }
}
