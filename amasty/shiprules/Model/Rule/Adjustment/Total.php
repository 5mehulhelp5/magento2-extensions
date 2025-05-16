<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\Rule\Adjustment;

/**
 * Total data model.
 */
class Total
{
    /**
     * @var float
     */
    private $price = 0.00;

    /**
     * @var float
     */
    private $notFreePrice = 0.00;

    /**
     * @var float
     */
    private $weight = 0.00;

    /**
     * @var float
     */
    private $notFreeWeight = 0.00;

    /**
     * @var float
     */
    private $qty = 0.00;

    /**
     * @var float
     */
    private $notFreeQty = 0.00;

    /**
     * @var bool
     */
    private $isFreeShipping = false;

    /**
     * @param float $price
     *
     * @return Total
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @param float $notFreePrice
     *
     * @return Total
     */
    public function setNotFreePrice(float $notFreePrice): self
    {
        $this->notFreePrice = $notFreePrice;

        return $this;
    }

    /**
     * @param float $weight
     *
     * @return Total
     */
    public function setWeight(float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @param float $notFreeWeight
     *
     * @return Total
     */
    public function setNotFreeWeight(float $notFreeWeight): self
    {
        $this->notFreeWeight = $notFreeWeight;

        return $this;
    }

    /**
     * @param float $qty
     *
     * @return Total
     */
    public function setQty(float $qty): self
    {
        $this->qty = $qty;

        return $this;
    }

    /**
     * @param float $notFreeQty
     *
     * @return Total
     */
    public function setNotFreeQty(float $notFreeQty): self
    {
        $this->notFreeQty = $notFreeQty;

        return $this;
    }

    /**
     * @param bool $isFreeShipping
     * @return $this
     */
    public function setFreeShipping(bool $isFreeShipping): self
    {
        $this->isFreeShipping = $isFreeShipping;

        return $this;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return float
     */
    public function getNotFreePrice(): float
    {
        return $this->notFreePrice;
    }

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * @return float
     */
    public function getNotFreeWeight(): float
    {
        return $this->notFreeWeight;
    }

    /**
     * @return float
     */
    public function getQty(): float
    {
        return $this->qty;
    }

    /**
     * @return float
     */
    public function getNotFreeQty(): float
    {
        return $this->notFreeQty;
    }

    /**
     * @return bool
     */
    public function getFreeShipping(): bool
    {
        return $this->isFreeShipping;
    }
}
