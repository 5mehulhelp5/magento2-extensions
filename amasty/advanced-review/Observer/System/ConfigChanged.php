<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Observer\System;

use Amasty\AdvancedReview\Model\ConfigProvider;
use Amasty\AdvancedReview\Model\Sources\WhoCanSubmit;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Review\Helper\Data;
use Magento\Store\Model\ScopeInterface;

class ConfigChanged implements ObserverInterface
{
    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var ReinitableConfigInterface
     */
    private $reinitableConfig;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    public function __construct(
        ReinitableConfigInterface $reinitableConfig,
        ScopeConfigInterface $config,
        WriterInterface $configWriter
    ) {
        $this->reinitableConfig = $reinitableConfig;
        $this->configWriter = $configWriter;
        $this->config = $config;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($observer->getData('store')) {
            $storeId = (int)$observer->getData('store');
            $websiteId = null;
        } elseif ($observer->getData('website')) {
            $storeId = null;
            $websiteId = (int)$observer->getData('website');
        } else {
            $storeId = null;
            $websiteId = null;
        }

        $name = $observer->getEvent()->getName();
        $catalogValue = $this->getCatalogValue($storeId, $websiteId);
        $moduleValue = $this->getAdvancedReviewValue($storeId, $websiteId);
        if ($catalogValue != $moduleValue) {
            switch ($name) {
                case 'admin_system_config_changed_section_catalog':
                    $this->saveAdvancedReviewValue($catalogValue, $storeId, $websiteId);
                    break;
                case 'admin_system_config_changed_section_amasty_advancedreview':
                    $this->saveCatalogValue($moduleValue, $storeId, $websiteId);
                    break;
            }
        }
    }

    private function saveAdvancedReviewValue(string $value, ?int $storeId = null, ?int $websiteId = null): void
    {
        if ($value == '1') {
            $value = WhoCanSubmit::ALL;
        } else {
            $value = WhoCanSubmit::REGISTERED;
        }

        $this->saveConfig(
            'amasty_advancedreview/' . ConfigProvider::XML_PATH_WHO_CAN_SUBMIT,
            $value,
            $storeId,
            $websiteId
        );
    }

    private function saveCatalogValue(string $value, ?int $storeId = null, ?int $websiteId = null): void
    {
        $this->saveConfig(Data::XML_REVIEW_GUETS_ALLOW, $value, $storeId, $websiteId);
    }

    private function saveConfig(string $path, string $value, ?int $storeId = null, ?int $websiteId = null): void
    {
        [$scopeType, $scopeId] = $this->resolveScope($storeId, $websiteId);
        $this->configWriter->save($path, $value, $scopeType, $scopeId);
        $this->reinitableConfig->reinit();
    }

    private function getCatalogValue(?int $storeId = null, ?int $websiteId = null): string
    {
        return $this->getConfigValue(Data::XML_REVIEW_GUETS_ALLOW, $storeId, $websiteId);
    }

    private function getAdvancedReviewValue(?int $storeId = null, ?int $websiteId = null): string
    {
        $whoCanSubmit = $this->getConfigValue(
            'amasty_advancedreview/' . ConfigProvider::XML_PATH_WHO_CAN_SUBMIT,
            $storeId,
            $websiteId
        );
        return $whoCanSubmit === WhoCanSubmit::ALL ? '1' : '0';
    }

    private function getConfigValue(string $path, ?int $storeId = null, ?int $websiteId = null): string
    {
        [$scopeType, $scopeId] = $this->resolveScope($storeId, $websiteId);
        return (string)$this->config->getValue($path, $scopeType, $scopeId);
    }

    private function resolveScope(?int $storeId = null, ?int $websiteId = null): array
    {
        if ($storeId !== null) {
            $scopeType = ScopeInterface::SCOPE_STORES;
            $scopeId = (int)$storeId;
        } elseif ($websiteId !== null) {
            $scopeType = ScopeInterface::SCOPE_WEBSITES;
            $scopeId = (int)$websiteId;
        } else {
            $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
            $scopeId = 0;
        }

        return [$scopeType, $scopeId];
    }
}
