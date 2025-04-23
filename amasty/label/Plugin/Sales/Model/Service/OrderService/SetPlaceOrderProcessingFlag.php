<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Plugin\Sales\Model\Service\OrderService;

use Amasty\Label\Model\Indexer\OrderProcessingFlag;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;

class SetPlaceOrderProcessingFlag
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

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterPlace(OrderManagementInterface $subject, OrderInterface $order): OrderInterface
    {
        $this->orderProcessingFlag->setIsOrderProcessing(true);
        $this->orderProcessingFlag->setIsShipmentProcessing(!$this->moduleManager->isEnabled('Magento_Inventory'));

        return $order;
    }
}
