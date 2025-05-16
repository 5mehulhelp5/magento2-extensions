<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Lazy Load for Magento 2 (System)
 */

namespace Amasty\LazyLoad\Model\Output\LazyConfig;

use Amasty\ImageOptimizer\Model\OptionSource\ReplaceStrategies;
use Amasty\LazyLoad\Model\ConfigProvider;
use Amasty\LazyLoad\Model\OptionSource\PreloadStrategy;
use Amasty\PageSpeedTools\Model\Output\PageType\GetConfigPathByPageType;
use Amasty\PageSpeedTools\Model\DeviceDetect;
use Magento\Framework\DataObject;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class LazyConfig extends DataObject
{
    public const IS_REPLACE_WITH_USER_AGENT = 'is_replace_with_user_agent';
    public const USER_AGENT_IGNORE_LIST = 'user_agent_ignore_list';
    public const IS_LAZY = 'is_lazy';
    public const LAZY_IGNORE_LIST = 'lazy_ignore_list';
    public const LAZY_SKIP_IMAGES = 'lazy_skip_images';
    public const LAZY_PRELOAD_STRATEGY = 'lazy_preload_strategy';
    public const LAZY_SCRIPT = 'lazy_script';

    /**
     * Image optimizer configuration for webp image replacement
     */
    public const IMG_OPTIMIZER_SUPPORT_THIRD_PARTY_IMAGE_ATTRIBUTES
        = 'img_optimizer_support_third_party_image_attributes';

    public const GENERAL = 'general';
    public const HOME = 'cms_index_index';
    public const CATEGORIES = 'catalog_category_view';
    public const PRODUCTS = 'catalog_product_view';
    public const CMS = 'cms_page_view';

    public const ENABLE_CUSTOM = [
        self::HOME => 'amoptimizer/replace_images_home/enable_custom_replace',
        self::CATEGORIES => 'amoptimizer/replace_images_categories/enable_custom_replace',
        self::PRODUCTS => 'amoptimizer/replace_images_products/enable_custom_replace',
        self::CMS => 'amoptimizer/replace_images_cms/enable_custom_replace'
    ];

    public const ENABLE_REPLACE = [
        self::GENERAL => 'amoptimizer/replace_images_general/replace_strategy',
        self::HOME => 'amoptimizer/replace_images_home/replace_strategy',
        self::CATEGORIES => 'amoptimizer/replace_images_categories/replace_strategy',
        self::PRODUCTS => 'amoptimizer/replace_images_products/replace_strategy',
        self::CMS => 'amoptimizer/replace_images_cms/replace_strategy'
    ];

    public const SUPPORT_THIRD_PARTY_IMAGE_ATTRIBUTES = [
        self::GENERAL => 'amoptimizer/replace_images_general/support_third_party_image_attributes',
        self::HOME => 'amoptimizer/replace_images_home/support_third_party_image_attributes',
        self::CATEGORIES => 'amoptimizer/replace_images_categories/support_third_party_image_attributes',
        self::PRODUCTS => 'amoptimizer/replace_images_products/support_third_party_image_attributes',
        self::CMS => 'amoptimizer/replace_images_cms/support_third_party_image_attributes'
    ];

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var GetConfigPathByPageType
     */
    private $getConfigPathByPageType;

    /**
     * @var DeviceDetect
     */
    private $deviceDetect;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var string
     */
    private $pageType;

    /**
     * @var string
     */
    private $configPath;

    /**
     * @var bool
     */
    private $isCustomLazyEnabled;

    public function __construct(
        DeviceDetect $deviceDetect,
        GetConfigPathByPageType $getConfigPathByPageType,
        ConfigProvider $configProvider,
        ScopeConfigInterface $scopeConfig,
        string $pageType = '',
        array $data = []
    ) {
        parent::__construct($data);
        $this->configProvider = $configProvider;
        $this->getConfigPathByPageType = $getConfigPathByPageType;
        $this->deviceDetect = $deviceDetect;
        $this->pageType = $pageType;
        $this->scopeConfig = $scopeConfig;

        if ($this->configProvider->isEnabled()) {
            $this->initialize();
        }
    }

    protected function initialize()
    {
        if (empty($this->getPageType())) {
            $this->configPath = $this->getConfigPathByPageType->execute();
            $pageType = $this->getConfigPathByPageType->getPageType();
        } else {
            $this->configPath = $this->getConfigPathByPageType->execute($this->getPageType());
            $pageType = $this->getPageType();
        }

        $this->isCustomLazyEnabled = (bool)$this->configProvider->getConfig(
            $this->configPath . ConfigProvider::PART_ENABLE_CUSTOM_LAZY
        );

        $this->initIsReplaceWithUserAgent();
        $this->initIsLazy();
        $this->initLazyIgnoreList();
        $this->initUserAgentIgnoreList();
        $this->initSkipImages();
        $this->initSkipStrategy();
        $this->initLazyScript();
        $this->initImgOptimizerSupportThirdPartyImgAttributes($pageType);
    }

    public function getPageType(): ?string
    {
        return $this->pageType;
    }

    public function setPageType(?string $pageType): void
    {
        $this->pageType = $pageType;
    }

    private function initIsReplaceWithUserAgent(): void
    {
        $this->setData(self::IS_REPLACE_WITH_USER_AGENT, $this->configProvider->isReplaceImagesUsingUserAgent());
    }

    private function initIsLazy(): void
    {
        $value = $this->isCustomLazyEnabled
            ? $this->configProvider->getConfig($this->configPath . ConfigProvider::PART_IS_LAZY)
            : $this->configProvider->isLazyLoad();

        $this->setData(self::IS_LAZY, (bool)$value);
    }

    private function initLazyIgnoreList(): void
    {
        $value = ($this->isCustomLazyEnabled)
            ? $this->configProvider->convertStringToArray(
                $this->configProvider->getConfig($this->configPath . ConfigProvider::PART_IGNORE)
            )
            : $this->configProvider->getIgnoreImages();

        $this->setData(self::LAZY_IGNORE_LIST, $value);
    }

    private function initUserAgentIgnoreList(): void
    {
        $value = ($this->configProvider->isReplaceImagesUsingUserAgent()
            && !empty($this->deviceDetect->getDeviceType())
        ) ? $this->configProvider->getReplaceImagesUsingUserAgentIgnoreList() : [];

        $this->setData(self::USER_AGENT_IGNORE_LIST, $value);
    }

    private function initSkipImages(): void
    {
        $type = ($this->configProvider->isReplaceImagesUsingUserAgent() && !empty($this->deviceDetect->getDeviceType()))
            ? '_' . $this->deviceDetect->getDeviceType()
            : '';
        $value = 0;

        if ($this->isCustomLazyEnabled) {
            if ($this->configProvider->getConfig($this->configPath . ConfigProvider::PART_PRELOAD)) {
                $value = $this->configProvider->customSkipImagesCount($this->configPath, $type);
            }
        } elseif ($this->configProvider->isPreloadImages()) {
            $value = $this->configProvider->skipImagesCount($type);
        }

        $this->setData(self::LAZY_SKIP_IMAGES, (int)$value);
    }

    private function initSkipStrategy(): void
    {
        if (!$this->configProvider->isReplaceImagesUsingUserAgent()) {
            $value = PreloadStrategy::SKIP_IMAGES;
            if ($this->isCustomLazyEnabled) {
                if ($this->configProvider->getConfig($this->configPath . ConfigProvider::PART_PRELOAD)) {
                    $value = $this->configProvider->getConfig($this->configPath . ConfigProvider::PART_STRATEGY);
                }
            } elseif ($this->configProvider->isPreloadImages()) {
                $value = $this->configProvider->getSkipStrategy();
            }
        } else {
            $value = PreloadStrategy::SMART_OPTIMIZATION;
        }

        $this->setData(self::LAZY_PRELOAD_STRATEGY, (int)$value);
    }

    private function initLazyScript(): void
    {
        $value = ($this->isCustomLazyEnabled)
            ? $this->configProvider->getConfig($this->configPath . ConfigProvider::PART_SCRIPT)
            : $this->configProvider->lazyLoadScript();

        $this->setData(self::LAZY_SCRIPT, (string)$value);
    }

    private function initImgOptimizerSupportThirdPartyImgAttributes($pageType)
    {
        $value = [];
        $scope = (isset(self::ENABLE_CUSTOM[$pageType])
            && $this->scopeConfig->isSetFlag(self::ENABLE_CUSTOM[$pageType], ScopeInterface::SCOPE_STORE))
            ? $pageType
            : self::GENERAL;

        $replaceStrategy = (int)$this->scopeConfig->getValue(self::ENABLE_REPLACE[$scope], ScopeInterface::SCOPE_STORE);
        if ($replaceStrategy !== 0) { //ImageOptimizer ReplaceStrategies::NONE
            $value = $this->convertStringToArray(
                str_replace(
                    "\r\n",
                    PHP_EOL,
                    (string)$this->scopeConfig->getValue(
                        self::SUPPORT_THIRD_PARTY_IMAGE_ATTRIBUTES[$scope],
                        ScopeInterface::SCOPE_STORE
                    )
                )
            );
        }

        $this->setData(self::IMG_OPTIMIZER_SUPPORT_THIRD_PARTY_IMAGE_ATTRIBUTES, $value);
    }

    public function convertStringToArray(?string $data, string $separator = PHP_EOL): array
    {
        return (!empty($data)) ? array_filter(array_map('trim', explode($separator, (string)$data))) : [];
    }
}
