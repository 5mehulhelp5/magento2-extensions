<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Plugin\Theme\Block\Html;

use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Theme\Block\Html\Topmenu;

class TopmenuPlugin
{
    public const PAGE_CACHE_ESI_ROUTE = 'page_cache/block/esi';

    /**
     * @var LayerResolver
     */
    private $layerResolver;

    public function __construct(LayerResolver $layerResolver)
    {
        $this->layerResolver = $layerResolver;
    }

    public function beforeGetUrl(Topmenu $subject, string $route = '', array $params = []): array
    {
        if ($route === self::PAGE_CACHE_ESI_ROUTE) {
            $catalogLayer = $this->layerResolver->get();
            if ($catalogLayer) {
                $currentCategory = $catalogLayer->getCurrentCategory();
                if ($currentCategory) {
                    $params['current_category'] = $currentCategory->getId();
                }
            }
        }

        return [$route, $params];
    }
}
