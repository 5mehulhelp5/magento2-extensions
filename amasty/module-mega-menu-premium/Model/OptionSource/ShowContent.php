<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Premium Base for Magento 2
 */

namespace Amasty\MegaMenuPremium\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class ShowContent implements OptionSourceInterface
{
    public const BEFORE = 0;

    public const AFTER = 1;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::BEFORE, 'label' => __('Before Subcategories')],
            ['value' => self::AFTER, 'label' => __('After Subcategories')]
        ];
    }
}
