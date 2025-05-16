<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Email;

use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\ProductAlert\Block\Email\AbstractEmail;
use Magento\ProductAlert\Block\Email\Price;
use Magento\ProductAlert\Block\Email\Stock;
use Magento\ProductAlert\Model\Email;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;

class AlertEmail extends Email
{
    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function send()
    {
        if ($this->_website === null || $this->_customer === null || !$this->isExistDefaultStore()) {
            return false;
        }

        $products = $this->getProducts();
        $templateConfigPath = $this->getTemplateConfigPath();
        if (!in_array($this->_type, ['price', 'stock']) || count($products) === 0 || !$templateConfigPath) {
            return false;
        }

        $storeId = (int) $this->getStoreId() ?: (int)$this->_customer->getStoreId();
        $store = $this->getStore($storeId);

        $block = $this->getBlock();
        $block->setStore($store)->reset();

        // Add products to the block
        foreach ($products as $product) {
            $product->setCustomerGroupId($this->_customer->getGroupId());
            $block->addProduct($product);
        }

        $templateId = $this->_scopeConfig->getValue(
            $templateConfigPath,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $alertGrid = $this->_appState->emulateAreaCode(
            Area::AREA_FRONTEND,
            [$block, 'toHtml']
        );

        $customerName = $this->_customerHelper->getCustomerName($this->_customer);
        $this->_transportBuilder->setTemplateIdentifier(
            $templateId
        )->setTemplateOptions(
            ['area' => Area::AREA_FRONTEND, 'store' => $storeId]
        )->setTemplateVars(
            [
                'customerName' => $customerName,
                'alertGrid' => $alertGrid,
            ]
        )->setFromByScope(
            $this->_scopeConfig->getValue(
                self::XML_PATH_EMAIL_IDENTITY,
                ScopeInterface::SCOPE_STORE,
                $storeId
            ),
            $storeId
        )->addTo(
            $this->_customer->getEmail(),
            $customerName
        )->setTemplateModel(
            \Amasty\Xnotif\Model\Email\Alert\Template::class
        )->getTransport()->sendMessage();

        return true;
    }

    /**
     * Retrieve the store for the email
     *
     * @param int $storeId
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    private function getStore(int $storeId): StoreInterface
    {
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * Retrieve the block for the email based on type
     *
     * @return Price|Stock
     * @throws LocalizedException
     */
    private function getBlock(): AbstractEmail
    {
        return $this->_type === 'price'
            ? $this->_getPriceBlock()
            : $this->_getStockBlock();
    }

    /**
     * Retrieve the products for the email based on type
     *
     * @return array
     */
    private function getProducts(): array
    {
        return $this->_type === 'price'
            ? $this->_priceProducts
            : $this->_stockProducts;
    }

    /**
     * Retrieve template config path based on type
     *
     * @return string
     */
    private function getTemplateConfigPath(): string
    {
        return $this->_type === 'price'
            ? self::XML_PATH_EMAIL_PRICE_TEMPLATE
            : self::XML_PATH_EMAIL_STOCK_TEMPLATE;
    }

    /**
     * Check if exists default store.
     *
     * @return bool
     */
    private function isExistDefaultStore(): bool
    {
        if (!$this->_website->getDefaultGroup() || !$this->_website->getDefaultGroup()->getDefaultStore()) {
            return false;
        }
        return true;
    }
}
