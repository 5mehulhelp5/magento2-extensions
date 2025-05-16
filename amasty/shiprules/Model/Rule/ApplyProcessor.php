<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\Rule;

use Amasty\Shiprules\Api\ShippingRuleApplierInterface as ApplierInterface;
use Amasty\Shiprules\Model\Rule\Quote\Address\RateRequest\Modifier as RequestModifier;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Error;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Shipping\Model\Shipping;

class ApplyProcessor
{
    /**
     * @var Applier|ApplierInterface
     */
    private $applier;

    /**
     * @var RequestModifier
     */
    private $requestModifier;

    /**
     * @var int[]
     */
    private $ruleItemIds = [];

    public function __construct(
        ApplierInterface $applier,
        RequestModifier $requestModifier
    ) {
        $this->applier = $applier;
        $this->requestModifier = $requestModifier;
    }

    /**
     * @param Shipping $shipping
     * @param RateRequest $request
     */
    public function process(Shipping $shipping, RateRequest $request): void
    {
        $result = $shipping->getResult();

        if (!$this->applier->canApplyAnyRule($request, $result->getAllRates())) {
            return;
        }

        //Save original result for correct return.
        $originalResult = clone $result;

        // Process rules with conditions
        $this->processPartialCartRules($shipping, $request, $originalResult->getAllRates());
        // Process other rules for whole cart
        $this->processFullCartRules($shipping, $request);

        // Apply changes per rate
        foreach ($originalResult->getAllRates() as $rate) {
            if ($rate instanceof Error) {
                continue;
            }

            $this->applier->applyAdjustment($rate);
        }

        $result->reset();
        $result->append($originalResult);
    }

    /**
     * Process rules which contains product conditions. We divide rule items for another 'virtual' cart and calculate
     * shipping prices for these products
     *
     * @param Shipping $shipping
     * @param RateRequest $origRequest
     * @param array $rates
     */
    private function processPartialCartRules(Shipping $shipping, RateRequest $origRequest, array $rates): void
    {
        /** @var Method $rate */
        foreach ($rates as $rate) {
            if ($rate instanceof Error) {
                continue;
            }

            $shippingRules = $this->applier->getRulesForCarrier($rate);

            // Save origin rate's value for rates that don't have rules.
            // To prevent changing this value in processFullCartRules()
            if (!$shippingRules) {
                $this->applier->calculateRateAdjustment($rate, $origRequest);
            }

            foreach ($shippingRules as $rule) {
                //Check all rate for `product tab` conditions and create new rate to recalculate prices
                $newRequest = $this->applier->getModifiedRequest($rate, $origRequest, $rule);

                if ($newRequest) {
                    $this->registerRuleItems($newRequest->getAllItems());
                    //If any condition is set, recollect ALL rates for specified products in rule.
                    $shipping->getResult()->reset();
                    $shipping->collectRates($newRequest);

                    //Re-calculate adjustment using new $rate value.
                    $newRate = $this->getNewRate($shipping, $rate);
                    $this->applier->calculateRateAdjustment($newRate, $newRequest);
                }
            }
        }
    }

    /**
     * Process full cart rules: 'replace' rules or other rules type with conditions which cover all current cart items
     *
     * @param Shipping $shipping
     * @param RateRequest $origRequest
     */
    private function processFullCartRules(Shipping $shipping, RateRequest $origRequest): void
    {
        $remainingItems = $this->getRemainingItems($origRequest);

        if ($remainingItems) {
            $newRequest = clone $origRequest;
            $newRequest->setAllItems($remainingItems);

            $newRequest = $this->requestModifier->modify($newRequest, null);

            $shipping->getResult()->reset();
            $shipping->collectRates($newRequest);
            $this->applier->calculateAdjustments($shipping->getResult()->getAllRates());
        }
    }

    /**
     * @param Shipping $shipping
     * @param Method $oldRate
     * @return Method
     */
    private function getNewRate(Shipping $shipping, Method $oldRate): Method
    {
        /** @var Method $rate */
        foreach ($shipping->getResult()->getRatesByCarrier($oldRate->getCarrier()) as $rate) {
            if ($rate->getCode() === $oldRate->getCode()
                && $rate->getMethod() === $oldRate->getMethod()
            ) {
                return $rate;
            }
        }

        return $oldRate;
    }

    /**
     * @param AbstractItem[] $items
     */
    private function registerRuleItems(array $items): void
    {
        foreach ($items as $item) {
            $itemId = $item->getId() ?? $item->getQuoteItemId();
            $this->ruleItemIds[] = (int)$itemId;
        }
    }

    /**
     * @param RateRequest $request
     * @return AbstractItem[]
     */
    private function getRemainingItems(RateRequest $request): array
    {
        $remainingItems = [];

        foreach ((array)$request->getAllItems() as $item) {
            $itemId = $item->getId() ?? $item->getQuoteItemId();
            if (!in_array((int)$itemId, $this->ruleItemIds, true)) {
                $remainingItems[] = $item;
            }
        }

        return $remainingItems;
    }
}
