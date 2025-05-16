<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Api\Data;

interface StockAlertInterface
{
    public const ALERT_ID = 'alert_id';
    public const ALERT_STOCK_ID = 'alert_stock_id';
    public const IS_RESTOCK = 'is_restock';

    public function setAlertId(int $id): StockAlertInterface;

    public function getAlertId(): int;

    public function setAlertStockId(int $alertStockId): StockAlertInterface;

    public function getAlertStockId(): int;

    public function setIsRestock(bool $isRestock): StockAlertInterface;

    public function isRestock(): bool;
}
