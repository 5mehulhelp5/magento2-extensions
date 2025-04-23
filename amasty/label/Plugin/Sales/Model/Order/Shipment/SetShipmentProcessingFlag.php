<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Plugin\Sales\Model\Order\Shipment;

use Amasty\Label\Model\Indexer\OrderProcessingFlag;
use Magento\Framework\Module\Manager as ModuleManager;

class SetShipmentProcessingFlag
{
    /**
     * @var OrderProcessingFlag
     */
    private $orderProcessingFlag;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    public function __construct(OrderProcessingFlag $orderProcessingFlag, ModuleManager $moduleManager)
    {
        $this->orderProcessingFlag = $orderProcessingFlag;
        $this->moduleManager = $moduleManager;
    }

    public function beforeAfterSave(): void
    {
        $this->orderProcessingFlag->setIsOrderProcessing(true);
        $this->orderProcessingFlag->setIsShipmentProcessing($this->moduleManager->isEnabled('Magento_Inventory'));
    }
}
