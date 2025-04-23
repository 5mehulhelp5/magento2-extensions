<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Rich Snippets for Magento 2
 */

namespace Amasty\SeoRichData\Model\Review;

use Amasty\SeoRichData\Model\Source\Product\RatingFormat;

class GetBestRating
{
    /**
     * @param int $ratingFormat
     * @return int
     */
    public function execute(int $ratingFormat): int
    {
        return $ratingFormat === RatingFormat::PERCENT ? 100 : 5;
    }
}
