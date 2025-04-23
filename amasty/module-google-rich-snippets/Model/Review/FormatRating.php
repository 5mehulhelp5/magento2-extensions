<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Rich Snippets for Magento 2
 */

namespace Amasty\SeoRichData\Model\Review;

class FormatRating
{
    /**
     * @param float $ratingValue
     * @param float $fromBestRating
     * @param int $toBestRating
     * @return float
     */
    public function execute(float $ratingValue, float $fromBestRating, int $toBestRating): float
    {
        return round($ratingValue * $toBestRating / $fromBestRating, 1);
    }
}
