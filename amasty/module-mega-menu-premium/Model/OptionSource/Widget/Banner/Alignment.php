<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Premium Base for Magento 2
 */

namespace Amasty\MegaMenuPremium\Model\OptionSource\Widget\Banner;

use Magento\Framework\Data\OptionSourceInterface;

class Alignment implements OptionSourceInterface
{
    public const CENTER = 0;

    public const LEFT = 1;

    public const RIGHT = 2;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::LEFT, 'label' => __('Left')],
            ['value' => self::CENTER, 'label' => __('Center')],
            ['value' => self::RIGHT, 'label' => __('Right')]
        ];
    }
}
