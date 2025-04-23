<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Rich Snippets for Magento 2
 */

namespace Amasty\SeoRichData\Model\Review\GetAggregateRating;

use Magento\Catalog\Model\Product;

interface RatingProviderInterface
{
    /**
     * @param Product $product
     * @param int $formatRating
     * @return array
     */
    public function execute(Product $product, int $formatRating): array;
}
