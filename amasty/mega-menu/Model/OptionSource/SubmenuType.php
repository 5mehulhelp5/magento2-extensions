<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class SubmenuType implements OptionSourceInterface
{
    public const WITHOUT_CONTENT = 0;

    public const WITH_CONTENT = 1;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::WITHOUT_CONTENT, 'label' => __('Column View')],
            ['value' => self::WITH_CONTENT, 'label' => __('Custom View')]
        ];
    }
}
