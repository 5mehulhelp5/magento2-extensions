<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\Rule\Adjustment\Total;

use Amasty\Shiprules\Model\Rule\Adjustment\Total;
use Amasty\Shiprules\Model\Rule\Adjustment\TotalFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;

class Registry
{
    /**
     * @var TotalFactory
     */
    private $totalFactory;

    /**
     * @var Calculator
     */
    private $calculator;

    /**
     * @var array
     */
    private $storage = [];

    public function __construct(
        TotalFactory $totalFactory,
        Calculator $calculator
    ) {
        $this->totalFactory = $totalFactory;
        $this->calculator = $calculator;
    }

    /**
     * @param RateRequest $rateRequest
     * @param string $hash
     * @return Total
     */
    public function getCalculatedTotal(RateRequest $rateRequest, string $hash): Total
    {
        $total = $this->storage[$hash] ?? null;

        if (!$total) {
            $this->storage[$hash] = $this->createCalculatedTotal($rateRequest);
        }

        return $this->storage[$hash];
    }

    /**
     * @param string $hash
     * @return Total|null
     */
    public function getByHash(string $hash): ?Total
    {
        return $this->storage[$hash] ?? null;
    }

    /**
     * @param string $hash
     */
    public function destroyTotal(string $hash): void
    {
        unset($this->storage[$hash]);
    }

    /**
     * @param RateRequest $rateRequest
     * @return Total
     */
    private function createCalculatedTotal(RateRequest $rateRequest): Total
    {
        $total = $this->totalFactory->create();

        $total->setFreeShipping((bool)$rateRequest->getFreeShipping());
        $this->calculator->calculate($total, $rateRequest);

        return $total;
    }

    public function _resetState(): void
    {
        $this->storage = [];
    }
}
