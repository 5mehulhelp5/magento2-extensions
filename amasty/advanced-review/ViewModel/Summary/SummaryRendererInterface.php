<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\ViewModel\Summary;

use Magento\Catalog\Model\Product;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;

interface SummaryRendererInterface
{
    public function render(ReviewCollection $collection, Product $product): string;
}
