<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Plugin\MagentoStore\Model;

use Magento\Framework\App\Router\PathConfigInterface;
use Magento\PageCache\Model\Config as FpcConfig;

/**
 * Fix Magento bug related with esi block rendering issue with ssl enabled
 */
class PathConfig
{
    /**
     * @var FpcConfig
     */
    private $cacheConfig;

    public function __construct(
        FpcConfig $cacheConfig
    ) {
        $this->cacheConfig = $cacheConfig;
    }

    /**
     * @param PathConfigInterface $subject
     * @param bool $result
     * @param string $path
     *
     * @return bool
     */
    public function afterShouldBeSecure(
        PathConfigInterface $subject,
        bool $result,
        string $path
    ): bool {
        if ($this->cacheConfig->getType() == FpcConfig::VARNISH
            && strpos($path, BaseUrlChecker::PAGE_CACHE_ESI_RENDER_IDENTIFIER_URL_PART) === 0
        ) {
            $result = false;
        }

        return $result;
    }
}
