<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Mega Menu PageBuilder for Magento 2 (System)
 */

namespace Amasty\MegaMenuPageBuilder\Model\Source;

/**
 * Class BlockLayout
 * @package Amasty\MegaMenuPageBuilder\Model\Source
 */
class BlockLayout implements \Magento\Framework\Option\ArrayInterface
{
    public const TYPE_GRID = 'grid';
    public const TYPE_SLIDER = 'slider';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::TYPE_SLIDER, 'label' => __('Slider')],
            ['value' => self::TYPE_GRID, 'label' => __('Grid')]
        ];
    }
}
