<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model;

use Amasty\Base\Model\ConfigProviderAbstract;

class ConfigProvider extends ConfigProviderAbstract
{
    /**
     * @var string '{section}/'
     */
    protected $pathPrefix = 'amxnotif/';

    private const GREETING_TEXT_EMAIL = 'general/customer_name';
    private const GDPR_ENABLED = 'gdrp/enabled';
    private const STOCK_CUSTOMER_GROUPS = 'stock/customer_group';
    private const STOCK_QTY_LIMITED = 'stock/email_limit';
    private const PRICE_CUSTOMER_GROUPS = 'price/customer_group';

    private const XML_PATH_ERROR_TEMPLATE = 'catalog/productalert_cron/error_email_template';
    private const XML_PATH_ERROR_IDENTITY = 'catalog/productalert_cron/error_email_identity';
    private const XML_PATH_ERROR_RECIPIENT = 'catalog/productalert_cron/error_email';

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isGDPREnabled(?int $storeId = null): bool
    {
        return (bool)$this->getValue(self::GDPR_ENABLED, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return array
     */
    public function getAllowedStockCustomerGroups(?int $storeId = null): array
    {
        $allowedGroups = $this->getValue(self::STOCK_CUSTOMER_GROUPS, $storeId);

        return explode(',', $allowedGroups);
    }

    /**
     * @param int|null $storeId
     * @return array
     */
    public function getAllowedPriceCustomerGroups(?int $storeId = null): array
    {
        $allowedGroups = $this->getValue(self::PRICE_CUSTOMER_GROUPS, $storeId);

        return explode(',', $allowedGroups);
    }

    /**
     * @return string|null
     */
    public function getErrorTemplate(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ERROR_TEMPLATE);
    }

    /**
     * @return string|null
     */
    public function getErrorIdentity(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ERROR_IDENTITY);
    }

    /**
     * @return string|null
     */
    public function getErrorRecipient(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ERROR_RECIPIENT);
    }

    public function isAlertEnabled(string $type, ?int $storeId = null): bool
    {
        return (bool)$this->getValue('catalog/productalert/allow_' . $type, $storeId);
    }

    public function isQtyLimitEnabled(): bool
    {
        return $this->isSetFlag(self::STOCK_QTY_LIMITED);
    }

    public function getGreetingTextForEmail(): string
    {
        return (string)$this->getValue(self::GREETING_TEXT_EMAIL);
    }

    public function isAdminNotificationEnabled(?int $storeId = null): bool
    {
        return $this->isSetFlag('admin_notifications/notify_admin', $storeId);
    }

    public function getAdminNotificationEmail(?int $storeId = null): string
    {
        return (string)$this->getValue('admin_notifications/stock_alert_email', $storeId);
    }

    public function getAdminNotificationSender(?int $storeId = null): string
    {
        return (string)$this->getValue('admin_notifications/sender_email_identity', $storeId);
    }

    public function getAdminNotificationTemplate(?int $storeId = null): string
    {
        return (string)$this->getValue('admin_notifications/notify_admin_template', $storeId);
    }

    public function isShowForOutOfStockConfigurable(): bool
    {
        return $this->isSetFlag('general/show_out_of_stock_only');
    }
}
