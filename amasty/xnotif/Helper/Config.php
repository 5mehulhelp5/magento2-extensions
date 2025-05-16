<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Helper;

use Amasty\Xnotif\Model\ConfigProvider;
use Amasty\Xnotif\Model\Source\Group;
use Laminas\Validator\EmailAddress;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    public const MODULE_PATH = 'amxnotif/';

    public const XML_PATH_LOW_STOCK_CONFIG = 'admin_notifications/low_stock_alert';
    public const XML_PATH_OUT_STOCK_CONFIG = 'admin_notifications/out_stock_alert';
    public const SHOW_OUT_OF_STOCK_ONLY = 'general/show_out_of_stock_only';

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    private $sessionFactory;

    /**
     * @var EmailAddress
     */
    private $emailAddressValidator;

    public function __construct(
        Context $context,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        EmailAddress $emailAddressValidator = null // TODO move to not optional
    ) {
        parent::__construct($context);
        $this->sessionFactory = $sessionFactory;
        $this->emailAddressValidator = $emailAddressValidator ?? ObjectManager::getInstance()->get(EmailAddress::class);
    }

    /**
     * @param $path
     * @param int $storeId
     * @return mixed
     */
    public function getModuleConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::MODULE_PATH . $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return bool
     */
    public function isLowStockNotifications()
    {
        return (bool)$this->getModuleConfig(self::XML_PATH_LOW_STOCK_CONFIG);
    }

    /**
     * @return bool
     */
    public function isOutStockNotifications()
    {
        return (bool)$this->getModuleConfig(self::XML_PATH_OUT_STOCK_CONFIG);
    }

    /**
     * Check if popup on
     *
     * @return int
     */
    public function isPopupForSubscribeEnabled()
    {
        return (int)$this->getModuleConfig('stock/with_popup');
    }

    /**
     * @return bool
     */
    public function isQtyLimitEnabled()
    {
        return (bool)$this->getModuleConfig('stock/email_limit');
    }

    /**
     * @return bool
     */
    public function isCategorySubscribeEnabled()
    {
        return (bool)$this->getModuleConfig('stock/subscribe_category');
    }

    /**
     * @Deprecated, use \Amasty\Xnotif\Model\ConfigProvider::isGDPREnabled()
     * @return bool
     */
    public function isGDRPEnabled()
    {
        return (bool)$this->getModuleConfig('gdrp/enabled');
    }

    /**
     * @return string
     */
    public function getGDRPText()
    {
        return $this->getModuleConfig('gdrp/text');
    }

    /**
     * @param null|int $storeId
     * @return bool
     */
    public function isAdminNotificationEnabled($storeId = null)
    {
        return ObjectManager::getInstance()->get(ConfigProvider::class)->isAdminNotificationEnabled((int)$storeId);
    }

    /**
     * @return bool
     */
    public function isParentImageEnabled()
    {
        return (bool)$this->getModuleConfig('general/account_image');
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return (string)$this->getModuleConfig('stock/placeholder');
    }

    /**
     * @param null|int $storeId
     * @return string
     */
    public function getAdminNotificationEmail($storeId = null)
    {
        return ObjectManager::getInstance()->get(ConfigProvider::class)->getAdminNotificationEmail((int)$storeId);
    }

    /**
     * @param null|int $storeId
     * @return string
     */
    public function getAdminNotificationSender($storeId = null)
    {
        return ObjectManager::getInstance()->get(ConfigProvider::class)->getAdminNotificationSender((int)$storeId);
    }

    /**
     * @param null|int $storeId
     * @return string
     */
    public function getAdminNotificationTemplate($storeId = null)
    {
        return (string)ObjectManager::getInstance()->get(ConfigProvider::class)->getAdminNotificationTemplate(
            (int)$storeId
        );
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        return (string)$this->getModuleConfig('general/customer_name');
    }

    /**
     * @return string
     */
    public function getTestEmail()
    {
        $email = (string)$this->getModuleConfig('general/test_notification_email');
        $email = $this->validateEmail($email);

        return $email;
    }

    /**
     * @return int
     */
    public function getMinQty()
    {
        $minQuantity = (int)$this->getModuleConfig('general/min_qty');
        $minQuantity = ($minQuantity < 0) ? 0 : $minQuantity;

        return $minQuantity;
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        $customerSession = $this->sessionFactory->create();

        return $customerSession->getCustomerId()
            && $customerSession->checkCustomerId($customerSession->getCustomerId());
    }

    /**
     * @Deprecated, use \Amasty\Xnotif\Model\ConfigProvider::getAllowedPriceCustomerGroups()
     * @return array
     */
    public function getAllowedPriceCustomerGroups()
    {
        $allowedGroups = $this->getModuleConfig('price/customer_group');

        return explode(',', $allowedGroups);
    }

    /**
     * @Deprecated, use \Amasty\Xnotif\Model\ConfigProvider::getAllowedStockCustomerGroups()
     * @return array
     */
    public function getAllowedStockCustomerGroups()
    {
        $allowedGroups = $this->getModuleConfig('stock/customer_group');

        return explode(',', $allowedGroups);
    }

    /**
     * @param $type
     * @return bool
     */
    public function allowForCurrentCustomerGroup($type)
    {
        if ($type == 'stock') {
            $allowedGroups = $this->getAllowedStockCustomerGroups();
        } else {
            $allowedGroups = $this->getAllowedPriceCustomerGroups();
        }

        if (in_array(Group::ALL_GROUPS, $allowedGroups)) {
            return true;
        }

        return in_array($this->getCustomerGroupId(), $allowedGroups);
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getQuantityBelow($storeId)
    {
        return $this->getModuleConfig('admin_notifications/qty_below', $storeId);
    }

    /**
     * @return int
     */
    protected function getCustomerGroupId()
    {
        return $this->sessionFactory->create()->getCustomerGroupId();
    }

    /**
     * @param $email
     *
     * @return string
     */
    protected function validateEmail($email)
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (!$this->emailAddressValidator->isValid($email)) {
            $email = '';
        }

        return $email;
    }

    public function isShowOutOfStockOnly(): bool
    {
        return (bool) $this->getModuleConfig(self::SHOW_OUT_OF_STOCK_ONLY);
    }
}
