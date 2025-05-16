<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Shop by Seo for Magento 2 (System)
 */

namespace Amasty\ShopbySeo\Model;

use Amasty\Base\Model\ConfigProviderAbstract;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider extends ConfigProviderAbstract
{
    public const AMSHOPBY_SEO_REL_NOFOLLOW = 'robots/rel_nofollow';

    public const GROUP_URL = 'url/';

    public const GROUP_CANONICAL = 'canonical/';

    /**
     * @var string
     */
    protected $pathPrefix = 'amasty_shopby_seo/';

    public function isEnableRelNofollow(): bool
    {
        return (bool) $this->getValue(self::AMSHOPBY_SEO_REL_NOFOLLOW);
    }

    /*
     * URL group.
     */

    /**
     * @param int|ScopeInterface|null $storeId
     */
    public function isSeoUrlEnabled($storeId = null): bool
    {
        return $this->isSetFlag(self::GROUP_URL . 'mode', $storeId);
    }

    public function isGenerateSeoByDefault(?int $storeId = null): bool
    {
        return $this->isSetFlag(self::GROUP_URL . 'is_generate_seo_default', $storeId);
    }

    public function getOptionSeparator(): string
    {
        return (string) $this->getValue(self::GROUP_URL . 'option_separator');
    }

    /*
     * Canonical group.
     * Canonical URL settings for different page types.
     */

    public function getCanonicalRoot(?int $storeId = null): string
    {
        return (string) $this->getValue(self::GROUP_CANONICAL . 'root', $storeId);
    }

    public function getCanonicalBrand(?int $storeId = null): string
    {
        return (string) $this->getValue(self::GROUP_CANONICAL . 'brand', $storeId);
    }

    public function getCanonicalCategory(?int $storeId = null): string
    {
        return (string) $this->getValue(self::GROUP_CANONICAL . 'category', $storeId);
    }

    public function isAddSuffix(?int $storeId = null): bool
    {
        return $this->isSetFlag(self::GROUP_URL . 'add_suffix_shopby', $storeId);
    }
}
