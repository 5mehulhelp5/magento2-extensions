<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Premium Base for Magento 2
 */

namespace Amasty\MegaMenuPremium\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class HamburgerAnimation implements OptionSourceInterface
{
    public const NONE = 'none';

    public const BOUNCE_IN_LEFT = 'bounce_in_left';

    public const FADE_IN_LEFT = 'fade_in_left';

    public const SLIDE_IN_LEFT = 'slide_in_left';

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::NONE, 'label' => __('None')],
            ['value' => self::BOUNCE_IN_LEFT, 'label' => __('bounceInLeft')],
            ['value' => self::FADE_IN_LEFT, 'label' => __('fadeInLeft')],
            ['value' => self::SLIDE_IN_LEFT, 'label' => __('slideInLeft')]
        ];
    }
}
