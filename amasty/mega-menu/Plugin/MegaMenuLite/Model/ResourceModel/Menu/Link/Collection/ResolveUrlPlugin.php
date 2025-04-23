<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Plugin\MegaMenuLite\Model\ResourceModel\Menu\Link\Collection;

use Amasty\MegaMenu\Model\ResourceModel\Menu\Frontend\ResolveUrl;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Link\Collection as LinkCollection;

class ResolveUrlPlugin
{
    /**
     * @var ResolveUrl
     */
    private $resolveUrl;

    public function __construct(
        ResolveUrl $resolveUrl
    ) {
        $this->resolveUrl = $resolveUrl;
    }

    public function aroundAddUrlToSelect(
        LinkCollection $linkCollection,
        callable $proceed,
        int $storeId
    ): void {
        $this->resolveUrl->joinLink($linkCollection, $storeId);
    }
}
