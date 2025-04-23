<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Special Promotions Base for Magento 2
 */

namespace Amasty\Rules\Model\Rule;

use Magento\Framework\App\ObjectManager;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Tax\Model\Config as TaxConfig;

class ItemCalculationPrice
{
    public const DEFAULT_PRICE = 0;
    public const DISCOUNTED_PRICE = 1;
    public const ORIGIN_PRICE = 2;
    public const ORIGIN_WITH_REVERT = 3;

    /**
     * Current rule price selector
     *
     * @var int
     */
    private $priceSelector = self::DEFAULT_PRICE;

    /**
     * @var \Magento\SalesRule\Model\Validator
     */
    private $validator;

    /**
     * @var TaxConfig
     */
    private $taxConfig;

    public function __construct(
        \Magento\SalesRule\Model\Validator $validator,
        ?TaxConfig $taxConfig = null // TODO Move to not optional
    ) {
        $this->validator = $validator;
        $this->taxConfig = $taxConfig ?? ObjectManager::getInstance()->get(TaxConfig::class);
    }

    /**
     * @param AbstractItem $item
     *
     * @return float
     */
    public function getItemPrice(AbstractItem $item): float
    {
        $price = $this->validator->getItemPrice($item);
        switch ($this->getPriceSelector()) {
            case self::DISCOUNTED_PRICE:
                $price -= $item->getDiscountAmount() / $item->getQty();
                break;
            case self::ORIGIN_PRICE:
            case self::ORIGIN_WITH_REVERT:
                $price = $item->getOriginalPrice();
                if ($this->taxConfig->discountTax($item->getStore()->getId())) {
                    $price += $item->getTaxAmount();
                }
                break;
        }

        return (float)$price;
    }

    /**
     * @param AbstractItem $item
     *
     * @return float
     */
    public function getItemBasePrice(AbstractItem $item): float
    {
        $price = $this->validator->getItemBasePrice($item);
        switch ($this->getPriceSelector()) {
            case self::DISCOUNTED_PRICE:
                $price -= $item->getBaseDiscountAmount() / $item->getQty();
                break;
            case self::ORIGIN_PRICE:
            case self::ORIGIN_WITH_REVERT:
                $price = $item->getBaseOriginalPrice();
                break;
        }

        return (float)$price;
    }

    /**
     * Return item original price
     *
     * @param AbstractItem $item
     *
     * @return float
     */
    public function getItemOriginalPrice(AbstractItem $item): float
    {
        $price = $this->validator->getItemOriginalPrice($item);
        switch ($this->getPriceSelector()) {
            case self::DISCOUNTED_PRICE:
                $price -= $item->getDiscountAmount() / $item->getQty();
                break;
            case self::ORIGIN_PRICE:
            case self::ORIGIN_WITH_REVERT:
                $price = $item->getProduct()->getPrice();
                break;
        }

        return (float)$price;
    }

    /**
     * Return item original price
     *
     * @param AbstractItem $item
     *
     * @return float
     */
    public function getItemBaseOriginalPrice(AbstractItem $item): float
    {
        $price = $this->validator->getItemBaseOriginalPrice($item);
        switch ($this->getPriceSelector()) {
            case self::DISCOUNTED_PRICE:
                $price -= $item->getBaseDiscountAmount() / $item->getQty();
                break;
            case self::ORIGIN_PRICE:
            case self::ORIGIN_WITH_REVERT:
                $price = $item->getBaseOriginalPrice();
                break;
        }

        return (float)$price;
    }

    /**
     * @param AbstractItem $item
     *
     * @return float
     */
    public function getFinalPriceRevert(AbstractItem $item): float
    {
        $origPrice = $item->getOriginalPrice();

        if ($this->taxConfig->discountTax($item->getStore()->getId())) {
            $origPrice += $item->getTaxAmount();
        }

        return $origPrice - $this->validator->getItemPrice($item);
    }

    /**
     * @param AbstractItem $item
     *
     * @return float
     */
    public function getBaseFinalPriceRevert(AbstractItem $item): float
    {
        $origPrice = $item->getBaseOriginalPrice();

        if ($this->taxConfig->discountTax($item->getStore()->getId())) {
            $origPrice += $item->getTaxAmount();
        }

        return $origPrice - $this->validator->getItemBasePrice($item);
    }

    /**
     * @param float $discount
     * @param AbstractItem $item
     *
     * @return float
     */
    public function resolveFinalPriceRevert(float $discount, AbstractItem $item): float
    {
        if ($this->getPriceSelector() !== self::ORIGIN_WITH_REVERT) {
            return $discount;
        }

        return max($discount - $this->getFinalPriceRevert($item), .0);
    }

    /**
     * @param float $discount
     * @param AbstractItem $item
     *
     * @return float
     */
    public function resolveBaseFinalPriceRevert(float $discount, AbstractItem $item): float
    {
        if ($this->getPriceSelector() !== self::ORIGIN_WITH_REVERT) {
            return $discount;
        }

        return max($discount - $this->getBaseFinalPriceRevert($item), .0);
    }

    /**
     * @return int
     */
    public function getPriceSelector(): int
    {
        return $this->priceSelector;
    }

    /**
     * @param int $priceSelector
     */
    public function setPriceSelector(int $priceSelector): void
    {
        $this->priceSelector = $priceSelector;
    }
}
