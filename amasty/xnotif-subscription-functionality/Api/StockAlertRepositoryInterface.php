<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Api;

use Amasty\XnotifSubscriptionFunctionality\Api\Data\StockAlertInterface;

interface StockAlertRepositoryInterface
{
    public function save(StockAlertInterface $stockAlert): StockAlertInterface;

    public function getByStockAlertId(int $id): StockAlertInterface;
}
