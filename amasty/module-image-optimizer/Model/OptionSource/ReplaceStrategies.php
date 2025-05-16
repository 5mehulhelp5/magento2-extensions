<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class ReplaceStrategies implements OptionSourceInterface
{
    public const NONE = 0;
    public const SMART_OPTIMIZATION = 1;

    /**
     * @var array
     */
    private $additionalStrategies;

    public function __construct(
        array $additionalStrategies = []
    ) {
        $this->additionalStrategies = $additionalStrategies;
    }

    public function toOptionArray(): array
    {
        $optionArray = [];
        foreach ($this->toArray() as $value => $label) {
            $optionArray[] = ['value' => $value, 'label' => $label];
        }

        return $optionArray;
    }

    public function toArray(): array
    {
        $baseStrategies = [
            self::NONE => __('None'),
            self::SMART_OPTIMIZATION => __('Smart Optimization')
        ];

        return array_merge($baseStrategies, $this->additionalStrategies);
    }
}
