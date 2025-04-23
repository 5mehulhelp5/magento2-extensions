<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class Font implements OptionSourceInterface
{
    public const BOLD = 700;

    public const REGULAR = 400;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::BOLD, 'label' => __('Bold')],
            ['value' => self::REGULAR, 'label' => __('Regular')]
        ];
    }
}
