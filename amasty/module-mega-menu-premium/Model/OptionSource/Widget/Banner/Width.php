<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Premium Base for Magento 2
 */

namespace Amasty\MegaMenuPremium\Model\OptionSource\Widget\Banner;

use Magento\Framework\Data\OptionSourceInterface;

class Width implements OptionSourceInterface
{
    public const AUTO = 0;

    public const CUSTOM = 1;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::AUTO, 'label' => __('Auto')],
            ['value' => self::CUSTOM, 'label' => __('Custom')]
        ];
    }
}
