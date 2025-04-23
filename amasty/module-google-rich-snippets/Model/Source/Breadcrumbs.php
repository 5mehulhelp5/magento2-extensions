<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Rich Snippets for Magento 2
 */

namespace Amasty\SeoRichData\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class Breadcrumbs implements ArrayInterface
{
    public const TYPE_LONG = 0;
    public const TYPE_SHORT = 1;

    public function toOptionArray()
    {
        return [
            self::TYPE_LONG => __('Default (Long)'),
            self::TYPE_SHORT => __('Short'),
        ];
    }
}
