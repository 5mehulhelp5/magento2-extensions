<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Plugin\Shipping\Model;

use Amasty\Shiprules\Api\ShippingRuleApplierInterface as ApplierInterface;
use Amasty\Shiprules\Model\Rule\Applier;
use Amasty\Shiprules\Model\Rule\ApplyProcessor;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Shipping;

class ShippingPlugin
{
    /**
     * @var Applier|ApplierInterface
     */
    private $applyProcessor;

    /**
     * @var boolean
     */
    private $lockCollectRates = false;

    public function __construct(ApplyProcessor $applyProcessor)
    {
        $this->applyProcessor = $applyProcessor;
    }

    /**
     * @param Shipping $subject
     * @param Shipping $result
     * @param RateRequest $request
     * @return Shipping
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCollectRates(Shipping $subject, Shipping $result, RateRequest $request): Shipping
    {
        if ($this->getLockCollectRates()) {
            return $result;
        }

        $this->lockCollectRates();
        $this->applyProcessor->process($result, $request);
        $this->unlockCollectRates();

        return $result;
    }

    /**
     * @return bool
     */
    private function getLockCollectRates(): bool
    {
        return $this->lockCollectRates;
    }

    private function lockCollectRates(): void
    {
        $this->lockCollectRates = true;
    }

    private function unlockCollectRates(): void
    {
        $this->lockCollectRates = false;
    }
}
