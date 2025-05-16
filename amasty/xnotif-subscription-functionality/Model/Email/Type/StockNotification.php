<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Model\Email\Type;

use Amasty\XnotifSubscriptionFunctionality\Model\ConfigProvider;

class StockNotification implements NotificationTypeInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    public function isNotificationEnabled(): bool
    {
        return $this->configProvider->isStockNotificationEnabled();
    }

    public function getTemplate(): string
    {
        return $this->configProvider->getStockTemplate();
    }
}
