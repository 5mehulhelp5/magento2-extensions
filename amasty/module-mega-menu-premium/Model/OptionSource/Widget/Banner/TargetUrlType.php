<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Premium Base for Magento 2
 */

namespace Amasty\MegaMenuPremium\Model\OptionSource\Widget\Banner;

use Magento\Framework\Data\OptionSourceInterface;

class TargetUrlType implements OptionSourceInterface
{
    public const BLANK = 0;

    public const CURRENT = 1;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::BLANK, 'label' => __('Blank')],
            ['value' => self::CURRENT, 'label' => __('Self')]
        ];
    }
}
