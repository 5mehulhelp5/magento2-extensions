<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\Rule\Items;

use Amasty\CommonRules\Model\Rule\Condition\Combine;
use Amasty\Shiprules\Api\Data\RuleInterface;
use Amasty\Shiprules\Model\Rule;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProductType;
use Magento\Quote\Model\Quote\Item;

class Provider
{
    /**
     * @var array
     */
    private $validItems = [];

    /**
     * reset storage
     */
    public function reset(): void
    {
        $this->validItems = [];
    }

    /**
     * @param RuleInterface|Rule $rule
     * @param Item[] $allItems
     * @return array
     */
    public function getValidItems(RuleInterface $rule, $allItems = []): array
    {
        $ruleId = $rule->getRuleId();

        if (isset($this->validItems[$ruleId])) {
            return $this->validItems[$ruleId];
        }

        $this->validItems[$ruleId] = [];

        /** @var Combine $actions */
        $actions = $rule->getActions();

        foreach ($allItems as $item) {
            $itemId = (int)($item->getId() ?? $item->getQuoteItemId());

            if ($item->getParentItem()) {
                continue;
            }

            if ($item->getProduct()->getTypeId() === ConfigurableProductType::TYPE_CODE) {
                foreach ($item->getChildren() as $child) {
                    if ($actions->validate($child)) {
                        $this->validItems[$ruleId][$itemId] = $item;
                        continue 2;
                    }
                }
            }

            if ($actions->validate($item)) {
                $this->validItems[$ruleId][$itemId] = $item;
            }
        }

        return $this->validItems[$ruleId];
    }

    /**
     * @param Item[] $allItem
     * @return array
     */
    public function getAllItemIds(array $allItem): array
    {
        $items = [];

        foreach ($allItem as $item) {
            if ($item->getParentItem()) {
                continue;
            }

            $itemId = $item->getId() ?? $item->getQuoteItemId();
            $items[] = (int)$itemId;
        }

        return $items;
    }

    public function _resetState(): void
    {
        $this->reset();
    }
}
