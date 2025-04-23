<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\OptionSource\Widget\Products;

use Magento\Framework\Data\OptionSourceInterface;

class BlockLayout implements OptionSourceInterface
{
    public const GRID = 'grid';

    public const SLIDER = 'slider';

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::GRID, 'label' => __('Grid')],
            ['value' => self::SLIDER, 'label' => __('Slider')]
        ];
    }
}
