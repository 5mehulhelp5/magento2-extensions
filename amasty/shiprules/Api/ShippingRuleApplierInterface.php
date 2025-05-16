<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Api;

/**
 * @api
 * @since 2.4.7
 * @since 2.6.0 added method getRulesForCarrier, for calculateRateAdjustment added argument
 */
interface ShippingRuleApplierInterface
{
    /**
     * Update $rate according adjustment data.
     *
     * @param \Magento\Quote\Model\Quote\Address\RateResult\Method $rate
     *
     * @return void
     */
    public function applyAdjustment(\Magento\Quote\Model\Quote\Address\RateResult\Method $rate);

    /**
     * Check it's necessary to recollect rate.
     * Return false, if modified request already calculated for rules.
     *
     * @param \Magento\Quote\Model\Quote\Address\RateResult\Method $rate
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @param \Amasty\Shiprules\Model\Rule $rule
     *
     * @return \Magento\Quote\Model\Quote\Address\RateRequest|bool
     */
    public function getModifiedRequest(
        \Magento\Quote\Model\Quote\Address\RateResult\Method $rate,
        \Magento\Quote\Model\Quote\Address\RateRequest $request,
        \Amasty\Shiprules\Model\Rule $rule
    );

    /**
     * Update adjustment value for each rate in $ratesArray.
     *
     * @param \Magento\Quote\Model\Quote\Address\RateResult\Method[] $ratesArray
     *
     * @return void
     */
    public function calculateAdjustments($ratesArray);

    /**
     * Update adjustment value according $rate and items of $newRequest data.
     *
     * @param \Magento\Quote\Model\Quote\Address\RateResult\Method $rate
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $newRequest
     *
     * @return void
     */
    public function calculateRateAdjustment(
        \Magento\Quote\Model\Quote\Address\RateResult\Method $rate,
        \Magento\Quote\Model\Quote\Address\RateRequest $newRequest
    );

    /**
     * Check all available rules, return true if at least one rule can be applied.
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @param \Magento\Quote\Model\Quote\Address\RateResult\Method[] $rates
     *
     * @return bool
     */
    public function canApplyAnyRule(\Magento\Quote\Model\Quote\Address\RateRequest $request, $rates);

    /**
     * Return registered valid rules for Carrier Method
     *
     * @param \Magento\Quote\Model\Quote\Address\RateResult\Method $rate
     *
     * @return \Amasty\Shiprules\Model\Rule[]
     */
    public function getRulesForCarrier($rate);

    /**
     * @return \Amasty\Shiprules\Model\Rule[]
     */
    public function getValidRules();
}
