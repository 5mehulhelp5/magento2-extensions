<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\Rule;

use Amasty\CommonRules\Model\OptionProvider\Provider\CalculationOptionProvider;
use Amasty\Shiprules\Api\Data\RuleInterface;
use Amasty\Shiprules\Api\ShippingRuleApplierInterface;
use Amasty\Shiprules\Model\Rule;
use Amasty\Shiprules\Model\Rule\Items\Provider as ItemsProvider;
use Amasty\Shiprules\Model\Rule\Provider as RulesProvider;
use Amasty\Shiprules\Model\Rule\Quote\Address\HashProvider;
use Amasty\Shiprules\Model\Rule\Quote\Address\RateRequest\Provider as RateRequestProvider;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Error;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Item;

/**
 * Rules applier service
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Applier implements ShippingRuleApplierInterface
{
    /**
     * Rules that passed validation for current rate request.
     *
     * @var Rule[]
     */
    private $validRules = [];

    /**
     * Rules that passed validation for current carrier.
     *
     * @var array(Rule[])
     */
    private $rulesByCarrier = [];

    /**
     * Id of all items from current rate request.
     *
     * @var array
     */
    private $allItemsId = [];

    /**
     * Need to calculate or get valid total value.
     *
     * @var null|string
     */
    private $currentHash = '';

    /**
     * @var array
     */
    private $resettableTypes;

    /**
     * @var Adjustment\Registry
     */
    private $adjustmentRegistry;

    /**
     * @var Adjustment\Calculator
     */
    private $adjustmentCalculator;

    /**
     * @var HashProvider
     */
    private $hashProvider;

    /**
     * @var ItemsProvider
     */
    private $itemsProvider;

    /**
     * @var RulesProvider
     */
    private $rulesProvider;

    /**
     * @var RateRequestProvider
     */
    private $rateRequestProvider;

    public function __construct(
        Adjustment\Registry $adjustmentRegistry,
        Adjustment\Calculator $adjustmentCalculator,
        ItemsProvider $itemsProvider,
        RulesProvider $rulesProvider,
        RateRequestProvider $rateRequestProvider,
        HashProvider $hashProvider,
        array $resettableTypes = [
            CalculationOptionProvider::CALC_REPLACE,
            CalculationOptionProvider::CALC_REPLACE_PRODUCT
        ]
    ) {
        $this->adjustmentRegistry = $adjustmentRegistry;
        $this->adjustmentCalculator = $adjustmentCalculator;
        $this->itemsProvider = $itemsProvider;
        $this->rulesProvider = $rulesProvider;
        $this->rateRequestProvider = $rateRequestProvider;
        $this->hashProvider = $hashProvider;
        $this->resettableTypes = $resettableTypes;
    }

    /**
     * @param Method $rate
     */
    public function applyAdjustment(Method $rate): void
    {
        $ratePrice = 0;
        $rate->setOldPrice($rate->getPrice());

        foreach ($this->adjustmentRegistry->getListForRate($rate) as $adjustment) {
            // To prevent applying negative value
            $ratePrice += max($adjustment->getValue(), 0);
            $range = $adjustment->getRateTotalRange();
            $ratePrice = max((float)$range[AdjustmentData::MIN], $ratePrice);

            if ((float)$range[AdjustmentData::MAX]) {
                $ratePrice = min((float)$range[AdjustmentData::MAX], $ratePrice);
            }
        }

        $rate->setPrice(max($ratePrice, 0));
    }

    /**
     * @param Method $rate
     * @param RateRequest $request
     * @param Rule $rule
     * @return bool|RateRequest
     */
    public function getModifiedRequest(
        Method $rate,
        RateRequest $request,
        Rule $rule
    ) {
        $specifiedProducts = $this->getSpecifiedProductsByRate($rate, $rule);

        if (empty($specifiedProducts)) {
            return false;
        }

        return $this->rateRequestProvider->getForItems($request, $rate, $specifiedProducts);
    }

    /**
     * @param Method[] $ratesArray
     */
    public function calculateAdjustments($ratesArray): void
    {
        foreach ($ratesArray as $rate) {
            if ($rate instanceof Error) {
                continue;
            }

            $adjustment = $this->adjustmentRegistry->get($rate, $this->currentHash);
            $adjustmentValue = 0;

            foreach ($this->getRulesForCarrier($rate) as $rule) {
                //Skip rules which contain product conditions
                if (array_diff($this->allItemsId, array_keys($this->itemsProvider->getValidItems($rule)))) {
                    continue;
                }

                $adjustment->setRateTotal($rule->getShipMin(), $rule->getShipMax());

                //Reset value for resettable rules
                $adjustmentValue = in_array((int)$rule->getCalc(), $this->resettableTypes, true) ? 0 : $adjustmentValue;
                $adjustmentValue += $this->adjustmentCalculator->calculateByRule(
                    $rule,
                    $rate,
                    $this->currentHash
                );
            }

            $adjustment->setValue($adjustment->getValue() + $adjustmentValue);
        }
    }

    /**
     * @param Method $rate
     * @param RateRequest $newRequest
     */
    public function calculateRateAdjustment(Method $rate, RateRequest $newRequest): void
    {
        $newHash = $this->hashProvider->getHash($newRequest);
        $adjustment = $this->adjustmentRegistry->get($rate, $newHash);
        $ids = $this->itemsProvider->getAllItemIds($newRequest->getAllItems());
        $adjustmentValue = 0;

        foreach ($this->getRulesForCarrier($rate) as $rule) {
            $ruleItemsIds = array_keys($this->itemsProvider->getValidItems($rule));

            //Skip rule if it doesn't contain product condition
            if (count($ids) !== count($ruleItemsIds) || array_diff($ids, $ruleItemsIds)) {
                continue;
            }

            $adjustment->setRateTotal($rule->getShipMin(), $rule->getShipMax());

            //Reset value for resettable rules
            $adjustmentValue = in_array((int)$rule->getCalc(), $this->resettableTypes, true) ? 0 : $adjustmentValue;
            $adjustmentValue += $this->adjustmentCalculator->calculateByRule(
                $rule,
                $rate,
                $newHash
            );
        }

        $adjustment->setValue($adjustment->getValue() + $adjustmentValue);
    }

    /**
     * @param RateRequest $request
     * @param Method[] $rates
     * @return bool
     */
    public function canApplyAnyRule(RateRequest $request, $rates): bool
    {
        $this->reset();
        $this->collectData($request);
        $canApply = false;

        if (!$this->validRules) {
            return $canApply;
        }

        foreach ($rates as $rate) {
            if ($rate instanceof Error) {
                continue;
            }

            foreach ($this->validRules as $rule) {
                if ($rule->match($rate)) {
                    $this->registerRuleForRate($rule, $rate);
                    $canApply = true;
                }
            }
        }

        return $canApply;
    }

    /**
     * @param Method $rate
     * @return Rule[]
     */
    public function getRulesForCarrier($rate): array
    {
        $rateCode = $rate->getCarrier() . '_' . $rate->getMethod();

        if (!isset($this->rulesByCarrier[$rateCode])) {
            return [];
        }

        return $this->rulesByCarrier[$rateCode];
    }

    /**
     * @return Rule[]
     */
    public function getValidRules(): array
    {
        return $this->validRules;
    }

    /**
     * Reset stored data
     */
    private function reset(): void
    {
        $this->rulesByCarrier = [];
        $this->rulesProvider->reset();
        $this->adjustmentRegistry->reset();
        $this->rateRequestProvider->reset();
    }

    /**
     * @param Method $rate
     * @param Rule $rule
     * @return Item[]
     */
    private function getSpecifiedProductsByRate(
        Method $rate,
        Rule $rule
    ): array {
        $specifiedProducts = [];
        $validItems = $this->itemsProvider->getValidItems($rule);

        if ($rule->match($rate)
            && array_diff($this->allItemsId, array_keys($validItems))
        ) {
            $specifiedProducts = $validItems;
        }

        return $specifiedProducts;
    }

    /**
     * @param RuleInterface $rule
     * @param Method $rate
     */
    private function registerRuleForRate(RuleInterface $rule, Method $rate): void
    {
        $rateCode = $rate->getCarrier() . '_' . $rate->getMethod();
        $ruleId = $rule->getRuleId();

        if (!isset($this->rulesByCarrier[$rateCode][$ruleId])) {
            $this->rulesByCarrier[$rateCode][$ruleId] = $rule;
        }
    }

    /**
     * @param RateRequest $request
     */
    private function collectData(RateRequest $request): void
    {
        $this->validRules = $this->rulesProvider->getValidRules($request);
        $this->currentHash = $this->hashProvider->getHash($request);
        $this->allItemsId = $this->itemsProvider->getAllItemIds($request->getAllItems());
    }

    public function _resetState(): void
    {
        $this->reset();
    }
}
