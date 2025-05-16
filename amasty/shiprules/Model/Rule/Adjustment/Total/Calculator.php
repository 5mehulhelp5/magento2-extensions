<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\Rule\Adjustment\Total;

use Amasty\Shiprules\Model\Rule\Adjustment\Total;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Item\AbstractItem;

class Calculator
{
    /**
     * @var array
     */
    private $calculateByParentTypes;

    public function __construct(
        array $calculateByParentTypes = [
            ProductType::TYPE_BUNDLE,
            Configurable::TYPE_CODE
        ]
    ) {
        $this->calculateByParentTypes = $calculateByParentTypes;
    }

    /**
     * @param Total $total
     * @param RateRequest $rateRequest
     * @return $this|Total
     */
    public function calculate(Total $total, RateRequest $rateRequest): Total
    {
        foreach ($rateRequest->getAllItems() as $item) {
            $shouldCalculateByParent = $item->getParentItem()
                && in_array($item->getParentItem()->getProduct()->getTypeId(), $this->calculateByParentTypes, true);

            if ($shouldCalculateByParent || $item->getProduct()->isVirtual()) {
                continue;
            }

            if ($item->getHasChildren() && $item->isShipSeparately()) {
                $this->calculateByBundle($total, $item);
            } else {
                $this->calculateByItem($total, $item);
                $notFreeQty = ($item->getQty() - $this->getFreeQty($item));

                $total->setWeight($total->getWeight() + $item->getWeight() * $item->getQty());
                $total->setNotFreeWeight(
                    $total->getNotFreeWeight() + $item->getWeight() * $notFreeQty
                );
            }
        }

        if ($rateRequest->getFreeShipping()) {
            $total
                ->setNotFreeWeight(0)
                ->setNotFreePrice(0)
                ->setNotFreeQty(0);
        }

        return $total;
    }

    /**
     * @param Total $total
     * @param AbstractItem $item
     */
    private function calculateByBundle(Total $total, AbstractItem $item): void
    {
        foreach ($item->getChildren() as $child) {
            if ($child->getProduct()->isVirtual()) {
                continue;
            }

            $childQty = $total->getQty() * $child->getQty();
            $notFreeChildQty = $item->getQty() * ($childQty - $this->getFreeQty($child));

            $this->calculateByItem($total, $child, $item->getQty());

            if (!$item->getProduct()->getWeightType()) {
                $total->setWeight($total->getWeight() + $item->getWeight() * $childQty);
                $total->setNotFreeWeight(
                    $total->getNotFreeWeight() + $item->getWeight() * $notFreeChildQty
                );
            }
        }

        if ($item->getProduct()->getWeightType()) {
            $notFreeQty = ($item->getQty() - $this->getFreeQty($item));

            $total->setWeight($total->getWeight() + $item->getWeight() * $item->getQty());
            $total->setNotFreeWeight(
                $total->getNotFreeWeight() + $item->getWeight() * $notFreeQty
            );
        }
    }

    /**
     * @param Total $total
     * @param AbstractItem $item
     * @param int $parentQty
     */
    private function calculateByItem(Total $total, AbstractItem $item, $parentQty = 1): void
    {
        $qty = $parentQty * $item->getQty();
        $notFreeQty = $qty - $parentQty * $this->getFreeQty($item);

        $total->setQty($total->getQty() + $qty);
        $total->setNotFreeQty($total->getNotFreeQty() + $notFreeQty);

        $total->setPrice($total->getPrice() + $item->getBasePrice() * $qty);
        $total->setNotFreePrice($total->getNotFreePrice() + $item->getBasePrice() * $notFreeQty);
    }

    /**
     * @param AbstractItem $item
     * @return int
     */
    private function getFreeQty(AbstractItem $item): int
    {
        $freeQty = 0;
        $freeShipping = $item->getFreeShipping();
        if ($freeShipping) {
            $freeQty = (int)(is_numeric($freeShipping) ? $freeShipping : $item->getQty());
        }

        return $freeQty;
    }
}
