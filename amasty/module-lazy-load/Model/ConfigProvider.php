<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Lazy Load for Magento 2 (System)
 */

namespace Amasty\LazyLoad\Model;

use Magento\Store\Model\ScopeInterface;

class ConfigProvider extends \Amasty\Base\Model\ConfigProviderAbstract
{
    /**
     * @var string
     */
    protected $pathPrefix = 'amoptimizer/';

    public const LAZY_LOAD = 'lazy_load_general/lazy_load';
    public const LAZY_LOAD_SCRIPT = 'lazy_load_general/lazy_load_script';
    public const PRELOAD_IMAGES = 'lazy_load_general/preload_images';
    public const SKIP_IMAGES_COUNT = 'lazy_load_general/skip_images_count';
    public const IGNORE_IMAGES = 'lazy_load_general/ignore_list';
    public const RESOLUTIONS = 'lazy_load_general/resolutions';
    public const REPLACE_IMAGES_USING_USER_AGENT = 'images/replace_images_using_user_agent';
    public const REPLACE_IMAGES_USING_USER_AGENT_IGNORE_LIST = 'images/replace_images_using_user_agent_ignore_list';
    public const SKIP_STRATEGY = 'lazy_load_general/preload_images_strategy';
    public const ASPECT_RATIO_ENABLED = 'lazy_load_general/aspect_enable';
    public const ASPECT_RATIO_LIST = 'lazy_load_general/aspect_list';

    public const PART_IS_LAZY = '/lazy_load';
    public const PART_SCRIPT = '/lazy_load_script';
    public const PART_STRATEGY = '/preload_images_strategy';
    public const PART_PRELOAD = '/preload_images';
    public const PART_SKIP = '/skip_images_count';
    public const PART_IGNORE = '/ignore_list';
    public const PART_ENABLE_CUSTOM_LAZY = '/enable_custom_lazyload';

    public function isEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue('amlazyload/general/enabled', ScopeInterface::SCOPE_STORE);
    }

    public function isLazyLoad(): bool
    {
        return $this->isSetFlag(self::LAZY_LOAD);
    }

    public function lazyLoadScript(): string
    {
        return (string)$this->getValue(self::LAZY_LOAD_SCRIPT);
    }

    public function isPreloadImages(): bool
    {
        return $this->isSetFlag(self::PRELOAD_IMAGES);
    }

    public function skipImagesCount(string $type = ''): int
    {
        return (int)$this->getValue(self::SKIP_IMAGES_COUNT . $type);
    }

    public function customSkipImagesCount(string $pageType, string $deviceType = ''): int
    {
        return (int)$this->getValue($pageType . self::PART_SKIP . $deviceType);
    }

    public function getIgnoreImages(): array
    {
        return $this->convertStringToArray($this->getValue(self::IGNORE_IMAGES));
    }

    public function getResolutions(): array
    {
        if ($this->getValue(self::RESOLUTIONS) !== '') {
            return explode(',', $this->getValue(self::RESOLUTIONS));
        }

        return [];
    }

    public function isReplaceImagesUsingUserAgent(): bool
    {
        return $this->isSetFlag(self::REPLACE_IMAGES_USING_USER_AGENT);
    }

    public function getReplaceImagesUsingUserAgentIgnoreList(): array
    {
        return $this->convertStringToArray($this->getValue(self::REPLACE_IMAGES_USING_USER_AGENT_IGNORE_LIST));
    }

    public function getSkipStrategy(): int
    {
        return (int)$this->getValue(self::SKIP_STRATEGY);
    }

    public function isAspectRatioEnabled(): bool
    {
        return (bool)$this->getValue(self::ASPECT_RATIO_ENABLED);
    }

    public function getAspectRatioList(): array
    {
        return $this->convertStringToArray($this->getValue(self::ASPECT_RATIO_LIST));
    }

    public function getConfig($path)
    {
        return $this->getValue($path);
    }

    public function getCustomValue($path)
    {
        return $this->scopeConfig->getValue($path);
    }

    public function convertStringToArray(?string $data, string $separator = PHP_EOL): array
    {
        if (empty($data)) {
            return [];
        }

        return array_filter(array_map('trim', explode($separator, $data)));
    }
}
