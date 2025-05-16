<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Model;

use Amasty\XnotifSubscriptionFunctionality\Api\Data\StockAlertInterface;
use Magento\Framework\Model\AbstractModel;

class StockAlert extends AbstractModel implements StockAlertInterface
{
    public function _construct()
    {
        $this->_init(ResourceModel\StockAlert::class);
        $this->setIdFieldName(StockAlertInterface::ALERT_ID);
    }

    public function setAlertId(int $id): StockAlertInterface
    {
        return $this->setData(StockAlertInterface::ALERT_ID, $id);
    }

    public function getAlertId(): int
    {
        return (int)$this->_getData(StockAlertInterface::ALERT_ID);
    }

    public function setAlertStockId(int $alertStockId): StockAlertInterface
    {
        return $this->setData(StockAlertInterface::ALERT_STOCK_ID, $alertStockId);
    }

    public function getAlertStockId(): int
    {
        return (int)$this->_getData(StockAlertInterface::ALERT_STOCK_ID);
    }

    public function setIsRestock(bool $isRestock): StockAlertInterface
    {
        return $this->setData(StockAlertInterface::IS_RESTOCK, $isRestock);
    }

    public function isRestock(): bool
    {
        return (bool)$this->_getData(StockAlertInterface::IS_RESTOCK);
    }
}
