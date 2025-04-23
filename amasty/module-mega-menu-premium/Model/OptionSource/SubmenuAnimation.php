<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Premium Base for Magento 2
 */

namespace Amasty\MegaMenuPremium\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class SubmenuAnimation implements OptionSourceInterface
{
    public const NONE = 'none';
    public const FADE_IN = 'fade_in';
    public const BOUNCE_IN_DOWN = 'bounce_in_down';
    public const FADE_IN_DOWN = 'fade_in_down';
    public const FLIP_IN_X = 'flip_in_x';
    public const FLIP_IN_Y = 'flip_in_y';
    public const ROTATE_IN_UP_LEFT = 'rotate_in_up_left';
    public const SLIDE_IN_UP = 'slide_in_up';
    public const SLIDE_IN_DOWN = 'slide_in_down';
    public const ROLL_IN = 'roll_in';
    public const ZOOM_IN = 'zoom_in';

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::NONE, 'label' => __('None')],
            ['value' => self::FADE_IN, 'label' => __('fadeIn')],
            ['value' => self::BOUNCE_IN_DOWN, 'label' => __('bounceInDown')],
            ['value' => self::FADE_IN_DOWN, 'label' => __('fadeInDown')],
            ['value' => self::FLIP_IN_X, 'label' => __('flipInX')],
            ['value' => self::FLIP_IN_Y, 'label' => __('flipInY')],
            ['value' => self::ROTATE_IN_UP_LEFT, 'label' => __('rotateInUpLeft')],
            ['value' => self::SLIDE_IN_UP, 'label' => __('slideInUp')],
            ['value' => self::SLIDE_IN_DOWN, 'label' => __('slideInDown')],
            ['value' => self::ROLL_IN, 'label' => __('rollIn')],
            ['value' => self::ZOOM_IN, 'label' => __('zoomIn')],
        ];
    }
}
