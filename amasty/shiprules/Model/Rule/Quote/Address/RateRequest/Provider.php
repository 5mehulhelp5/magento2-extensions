<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\Rule\Quote\Address\RateRequest;

use Amasty\Shiprules\Model\Rule\Items\Provider as ItemsProvider;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;

class Provider
{
    /**
     * @var Modifier
     */
    private $modifier;

    /**
     * @var ItemsProvider
     */
    private $itemsProvider;

    /**
     * @var RateRequest[]
     */
    private $calculatedRequests = [];

    public function __construct(
        Modifier $modifier,
        ItemsProvider $itemsProvider
    ) {
        $this->modifier = $modifier;
        $this->itemsProvider = $itemsProvider;
    }

    public function reset(): void
    {
        $this->calculatedRequests = [];
    }

    /**
     * @param RateRequest $sourceRateRequest
     * @param Method $rate
     * @param array $requestItems
     * @return RateRequest|false if request for specified items was already calculated
     */
    public function getForItems(RateRequest $sourceRateRequest, Method $rate, array $requestItems)
    {
        $cacheKey = $this->getCacheKey($rate, $requestItems);

        if (isset($this->calculatedRequests[$cacheKey])) {
            return false;
        }

        $newRequest = clone $sourceRateRequest;
        $newRequest->setAllItems(array_values($requestItems));
        $this->calculatedRequests[$cacheKey] = $this->modifier->modify($newRequest, $rate);

        return $this->calculatedRequests[$cacheKey];
    }

    /**
     * @param Method $rate
     * @param array $items
     * @return string
     */
    private function getCacheKey(Method $rate, array $items): string
    {
        $cacheKey = $rate->getCarrier() . '_' . $rate->getMethod();
        $itemIds = $this->itemsProvider->getAllItemIds($items);
        sort($itemIds);

        return $cacheKey . '_' . implode('_', $itemIds);
    }

    public function _resetState(): void
    {
        $this->reset();
    }
}
