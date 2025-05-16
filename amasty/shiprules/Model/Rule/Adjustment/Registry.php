<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\Rule\Adjustment;

use Amasty\Shiprules\Model\Rule\AdjustmentData as Adjustment;
use Amasty\Shiprules\Model\Rule\AdjustmentDataFactory as Factory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;

class Registry
{
    public const KEY_SEPARATOR = '~';

    /**
     * @var array
     */
    private $storage = [];

    /**
     * @var Factory
     */
    private $adjustmentFactory;

    public function __construct(Factory $adjustmentFactory)
    {
        $this->adjustmentFactory = $adjustmentFactory;
    }

    /**
     * @param Method $rate
     * @param string $addressHash
     * @return Adjustment
     */
    public function get(Method $rate, string $addressHash): Adjustment
    {
        $rateKey = $this->getRateKey($rate);

        if (!isset($this->storage[$rateKey][$addressHash])) {
            $this->storage[$rateKey][$addressHash] = $this->createForRate($rate);
        }

        return $this->storage[$rateKey][$addressHash];
    }

    /**
     * @param Adjustment $adjustment
     * @param string $addressHash
     *
     * @return Registry
     */
    public function set(Adjustment $adjustment, string $addressHash): self
    {
        $this->storage[$addressHash][$adjustment->getRateKey()] = $adjustment;

        return $this;
    }

    /**
     * @param Method $rate
     * @return Adjustment[]
     */
    public function getListForRate(Method $rate): array
    {
        $rateKey = $this->getRateKey($rate);

        return (array)($this->storage[$rateKey] ?? []);
    }

    public function reset(): void
    {
        $this->storage = [];
    }

    /**
     * @param Method $rate
     * @return Adjustment
     */
    private function createForRate(Method $rate): Adjustment
    {
        $adjustment = $this->adjustmentFactory->create();

        $adjustment->setRateKey($this->getRateKey($rate));
        $adjustment->setValue((float)$rate->getPrice());

        return $adjustment;
    }

    /**
     * @param Method $rate
     * @return string
     */
    private function getRateKey(Method $rate): string
    {
        return $rate->getCarrier() . self::KEY_SEPARATOR . $rate->getMethod();
    }
}
