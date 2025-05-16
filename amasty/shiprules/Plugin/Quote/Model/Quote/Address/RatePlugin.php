<?php

namespace Amasty\Shiprules\Plugin\Quote\Model\Quote\Address;

/**
 * Plugin to save rate old price.
 */
class RatePlugin
{
    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\AbstractResult
     */
    private $rate;

    /**
     * @param \Magento\Quote\Model\Quote\Address\Rate $subject
     * @param \Magento\Quote\Model\Quote\Address\RateResult\AbstractResult $rate
     *
     * @see \Magento\Quote\Model\Quote\Address\Rate::importShippingRate()
     */
    public function beforeImportShippingRate(
        \Magento\Quote\Model\Quote\Address\Rate $subject,
        \Magento\Quote\Model\Quote\Address\RateResult\AbstractResult $rate
    ) {
        $this->rate = $rate;
        $subject->setOldPrice($this->rate->getOldPrice());
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Rate $subject
     * @param \Magento\Quote\Model\Quote\Address\Rate $result
     *
     * @see \Magento\Quote\Model\Quote\Address\Rate::importShippingRate()
     */
    public function afterImportShippingRate(
        \Magento\Quote\Model\Quote\Address\Rate $subject,
                                                $result
    ) {
        $result->setOldPrice($this->rate->getOldPrice());

        return $result;
    }

    public function _resetState(): void
    {
        $this->rate = null;
    }
}
