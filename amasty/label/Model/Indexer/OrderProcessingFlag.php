<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Indexer;

class OrderProcessingFlag
{
    /**
     * @var bool
     */
    private $isOrderProcessing = false;

    /**
     * @var bool
     */
    private $isShipmentProcessing = false;

    public function isOrderProcessing(): bool
    {
        return $this->isOrderProcessing;
    }

    public function setIsOrderProcessing(bool $isOrderProcessing): void
    {
        $this->isOrderProcessing = $isOrderProcessing;
    }

    public function isShipmentProcessing(): bool
    {
        return $this->isShipmentProcessing;
    }

    public function setIsShipmentProcessing(bool $isShipmentProcessing): void
    {
        $this->isShipmentProcessing = $isShipmentProcessing;
    }
}
