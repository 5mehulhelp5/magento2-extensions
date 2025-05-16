<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Model;

use Amasty\Base\Model\ConfigProviderAbstract;

class ConfigProvider extends ConfigProviderAbstract
{
    public const SUBSCRIBE_RECOMMENDED_BLOCKS = 'stock/subscribe_recommended_blocks';
    public const RESTOCK_ALERT = 'stock/restock_alert';
    public const PRICE_NOTIFICATION_ENABLED = 'customer_notifications/price_notification_enabled';
    public const PRICE_TEMPLATE = 'customer_notifications/price_template';
    public const STOCK_NOTIFICATION_ENABLED = 'customer_notifications/stock_notification_enabled';
    public const STOCK_TEMPLATE = 'customer_notifications/stock_template';
    public const SENDER_EMAIL = 'customer_notifications/sender_email_stock_price';

    /**
     * @var string
     */
    protected $pathPrefix = 'amxnotif/';

    public function isSubscriptionForRecommendedBlocksEnabled(?int $storeId = null): bool
    {
        return $this->isSetFlag(self::SUBSCRIBE_RECOMMENDED_BLOCKS, $storeId);
    }

    public function isRestockAlertEnabled(?int $storeId = null): bool
    {
        return $this->isSetFlag(self::RESTOCK_ALERT, $storeId);
    }

    public function isPriceNotificationEnabled(?int $storeId = null): bool
    {
        return $this->isSetFlag(self::PRICE_NOTIFICATION_ENABLED, $storeId);
    }

    public function getPriceTemplate(?int $storeId = null): string
    {
        return (string)$this->getValue(self::PRICE_TEMPLATE, $storeId);
    }

    public function isStockNotificationEnabled(?int $storeId = null): bool
    {
        return $this->isSetFlag(self::STOCK_NOTIFICATION_ENABLED, $storeId);
    }

    public function getStockTemplate(?int $storeId = null): string
    {
        return (string)$this->getValue(self::STOCK_TEMPLATE, $storeId);
    }

    public function getEmailSender(?int $storeId = null): string
    {
        return (string)$this->getValue(self::SENDER_EMAIL, $storeId);
    }
}
