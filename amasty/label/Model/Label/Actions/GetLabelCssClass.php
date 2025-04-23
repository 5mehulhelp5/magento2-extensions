<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\Actions;

class GetLabelCssClass
{
    public const DEFAULT_CLASS = 'top-left';

    /**
     * @var string[]
     */
    private $horizontalPositions = ['left', 'center', 'right'];

    /**
     * @var string[]
     */
    private $verticalPositions   = ['top', 'middle', 'bottom'];

    public function execute(int $position): string
    {
        $allAvailable = $this->getCssClasses();

        return $allAvailable[$position] ?? self::DEFAULT_CLASS;
    }

    /**
     * @return string[]
     */
    private function getCssClasses(): array
    {
        $result = [];

        foreach ($this->verticalPositions as $first) {
            foreach ($this->horizontalPositions as $second) {
                $result[] = "{$first}-{$second}";
            }
        }

        return $result;
    }
}
