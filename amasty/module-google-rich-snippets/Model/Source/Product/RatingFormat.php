<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Rich Snippets for Magento 2
 */

namespace Amasty\SeoRichData\Model\Source\Product;

use Magento\Framework\Data\OptionSourceInterface;

class RatingFormat implements OptionSourceInterface
{
    public const PERCENT = 0;
    public const NUMERIC = 1;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            self::PERCENT => __('Percentage Scale '),
            self::NUMERIC => __('Numeric Scale')
        ];
    }
}
