<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\Rule\Quote\Address\RateRequest;

use Amasty\Shiprules\Model\Rule\Adjustment\Total\Registry as TotalRegistry;
use Amasty\Shiprules\Model\Rule\Quote\Address\HashProvider;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;

class Modifier
{
    /**
     * @var TotalRegistry
     */
    private $totalRegistry;

    /**
     * @var HashProvider
     */
    private $hashProvider;

    public function __construct(
        TotalRegistry $totalRegistry,
        HashProvider $hashProvider
    ) {
        $this->totalRegistry = $totalRegistry;
        $this->hashProvider = $hashProvider;
    }

    /**
     * @param RateRequest $rateRequest
     * @param Method|null $rate
     * @return RateRequest
     */
    public function modify(RateRequest $rateRequest, ?Method $rate = null): RateRequest
    {
        $hash = $this->hashProvider->getHash($rateRequest);
        $total = $this->totalRegistry->getCalculatedTotal($rateRequest, $hash);

        if ($rate) {
            $rateRequest->setLimitCarrier($rate->getCarrier());
            $rateRequest->setLimitMethod($rate->getMethod());
        }

        $rateRequest->setPackageValue($total->getPrice());
        $rateRequest->setPackageWeight($total->getWeight());
        $rateRequest->setPackageQty($total->getQty());
        $rateRequest->setFreeMethodWeight($total->getNotFreeWeight());
        $rateRequest->setPackageValueWithDiscount($rateRequest->getPackageValue());
        $rateRequest->setPackagePhysicalValue($rateRequest->getPackageValue());

        return $rateRequest;
    }
}
