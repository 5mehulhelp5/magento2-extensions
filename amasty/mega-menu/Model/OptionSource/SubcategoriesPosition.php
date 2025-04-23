<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\OptionSource;

class SubcategoriesPosition
{
    public const NOT_SHOW = 0;

    public const LEFT = 1;

    public const TOP = 2;

    public function toOptionArray(bool $notShowOptions = false): array
    {
        $options = [
            self::LEFT => ['value' => self::LEFT, 'label' => __('Vertical')],
            self::TOP => ['value' => self::TOP, 'label' => __('Horizontal')]
        ];

        if ($notShowOptions) {
            $options[self::NOT_SHOW] = ['value' => self::NOT_SHOW, 'label' => __('Do not show')];
        }

        return $options;
    }
}
