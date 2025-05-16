<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class ResolutionsLib implements OptionSourceInterface
{
    /**
     * @var string[] ['value' => 'label']
     */
    private $libs;

    public function __construct(
        array $libs = []
    ) {
        $this->libs = $libs;
    }

    public function toOptionArray(): array
    {
        $result = [];
        foreach ($this->toArray() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }

        return $result;
    }

    public function toArray(): array
    {
        return $this->libs;
    }
}
