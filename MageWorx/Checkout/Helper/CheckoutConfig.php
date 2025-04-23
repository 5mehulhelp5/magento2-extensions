<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Helper;

use Magento\Customer\Model\AccountManagement;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\AbstractHelper as MagentoAbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Model\ScopeInterface;
use MageWorx\Checkout\Api\CheckoutConfigInterface;
use MageWorx\Checkout\Helper\PaymentMethods as PaymentMethodsHelper;

/**
 * Class Data
 */
class CheckoutConfig extends AbstractHelper implements CheckoutConfigInterface
{
    /**
     * @var PaymentMethods
     */
    private $paymentMethodsHelper;

    /**
     * CheckoutConfig constructor.
     *
     * @param Context $context
     * @param PaymentMethods $paymentMethodsHelper
     */
    public function __construct(
        Context $context,
        PaymentMethodsHelper $paymentMethodsHelper
    ) {
        $this->paymentMethodsHelper = $paymentMethodsHelper;
        parent::__construct($context);
    }

    /**
     * Is module enabled
     *
     * @param int:null $storeId
     * @return bool
     */
    public function isEnabled(int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            static::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get checkout page main title
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCheckoutPageTitle(int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            static::XML_PATH_CHECKOUT_PAGE_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Is coupon code block (input) visible
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isCouponCodeBlockVisible(int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            static::XML_PATH_COUPON_CODE_VISIBLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Tooltip message for coupon code input
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCouponCodeTooltip(int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            static::XML_PATH_COUPON_CODE_TOOLTIP,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @inheritDoc
     */
    public function getDefaultShippingMethod(int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            static::XML_PATH_DEFAULT_SHIPPING_METHOD,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @inheritDoc
     */
    public function getDefaultPaymentMethod(int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            static::XML_PATH_DEFAULT_PAYMENT_METHOD,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @inheritDoc
     */
    public function getLabels(int $storeId = null): ?array
    {
        return $this->scopeConfig->getValue(
            static::XML_PATH_LABELS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Order comments visibility
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isOrderCommentsEnabled(int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            static::XML_PATH_ORDER_COMMENTS_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getOrderCommentsLabel(int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            static::XML_PATH_ORDER_COMMENTS_LABEL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @inheritDoc
     */
    public function isDisplayPaymentMethodImage(int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            static::XML_PATH_DISPLAY_PAYMENT_METHOD_IMAGES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @inheritDoc
     */
    public function getPaymentMethodsImages(int $storeId = null): array
    {
        return $this->paymentMethodsHelper->getPaymentMethodsConfiguration($storeId);
    }

    /**
     * @inheritDoc
     */
    public function isEmailSubscriptionEnabled(int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            static::XML_PATH_EMAIL_SUBSCRIPTION_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @inheritDoc
     */
    public function getEmailSubscriptionTitle(int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            static::XML_PATH_EMAIL_SUBSCRIPTION_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @inheritDoc
     */
    public function isCreateAccountEnabled(int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            static::XML_PATH_CREATE_ACCOUNT_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Is forced registration enabled;
     * Will be useful in case with guest checkout and virtual quote combination;
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isForcedCreateAccountEnabled(int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            static::XML_PATH_CREATE_ACCOUNT_FORCED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @inheritDoc
     */
    public function getCreateAccountTitle(int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            static::XML_PATH_CREATE_ACCOUNT_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @inheritDoc
     */
    public function isCreateAccountCheckedByDefault(int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            static::XML_PATH_CREATE_ACCOUNT_CHECKED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Checkout agreements message, visible below the place order button
     *
     * @param int|null $storeId
     * @return string|null
     */
    public function getCheckoutAgreementsMessage(int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            static::XML_PATH_CHECKOUT_AGREEMENTS_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Is need to merge JS and CSS files on the checkout page
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isMergeJsCssEnabled(int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            static::XML_PATH_MERGE_JS_CSS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Minimum length for the create new account password field
     *
     * @return int
     */
    public function getMinPasswordLength(): int
    {
        return (int)$this->scopeConfig->getValue(AccountManagement::XML_PATH_MINIMUM_PASSWORD_LENGTH);
    }

    /**
     * Get number of password required character classes
     *
     * @return int
     */
    public function getRequiredCharacterClassesNumber(): int
    {
        return (int)$this->scopeConfig->getValue(AccountManagement::XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER);
    }

    /**
     * Is custom shipping address template enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isUseCustomShippingAddressTemplate(int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            static::XML_PATH_USE_CUSTOM_SHIPPING_ADDRESS_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
