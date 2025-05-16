<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Plugins\Sales\Model\Order\Shipment;

use Amasty\Xnotif\Helper\Config;
use Amasty\Xnotif\Model\Notification\LowStockAlert as NotificationModel;
use Amasty\Xnotif\Model\ResourceModel\Inventory as InventoryResolver;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Sales\Model\Order\Shipment;
use Psr\Log\LoggerInterface;

class SendLowStockAlert
{
    /**
     * @var NotificationModel
     */
    private $notificationModel;

    /**
     * @var InventoryResolver
     */
    private $inventoryResolver;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        InventoryResolver $inventoryResolver,
        NotificationModel $notificationModel,
        LoggerInterface $logger,
        Config $config
    ) {
        $this->notificationModel = $notificationModel;
        $this->inventoryResolver = $inventoryResolver;
        $this->logger = $logger;
        $this->config = $config;
    }

    public function afterAfterSave(Shipment $subject): Shipment
    {
        if ($this->config->isLowStockNotifications() || $this->config->isOutStockNotifications()) {
            $this->processShipment($subject);
        }

        return $subject;
    }

    private function processShipment(Shipment $shipment): void
    {
        try {
            $sourceCode = $this->getSourceCode($shipment);
            $items = $shipment->getItems();
            $itemsSku = array_map(function ($productItem) {
                return $productItem->getSku();
            }, $items);
            $lowStockItemsSku = $this->inventoryResolver->getLowStockItemsSku($itemsSku, $sourceCode);

            foreach ($items as $key => $item) {
                if ($item->getQty() <= 0
                    || ($item->getOrderItem()->getProductType() !== ProductType::TYPE_SIMPLE
                        && $item->getOrderItem()->isDummy(true))
                    || !in_array($item->getSku(), $lowStockItemsSku)
                ) {
                    unset($items[$key]);
                }
            }

            if (!empty($items)) {
                $this->notificationModel->notify($items, $sourceCode);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }

    private function getSourceCode(Shipment $shipment): ?string
    {
        $sourceCode = null;
        $attributes = $shipment->getExtensionAttributes();

        if (!empty($attributes)
            && method_exists($attributes, 'getSourceCode')
            && $attributes->getSourceCode()
        ) {
            $sourceCode = $attributes->getSourceCode();
        }

        return $sourceCode;
    }
}
