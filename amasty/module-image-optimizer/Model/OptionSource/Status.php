<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\OptionSource;

use Amasty\PageSpeedTools\Model\OptionSource\ToOptionArrayTrait;
use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    public const DISABLED = 0;
    public const ENABLED = 1;

    use ToOptionArrayTrait;

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::DISABLED => __('Disabled'),
            self::ENABLED => __('Enabled'),
        ];
    }
}
