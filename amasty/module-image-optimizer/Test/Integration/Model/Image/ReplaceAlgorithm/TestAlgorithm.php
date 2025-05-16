<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Test\Integration\Model\Image\ReplaceAlgorithm;

use Amasty\PageSpeedTools\Model\Image\ReplaceAlgorithm\ReplaceAlgorithmInterface;

class TestAlgorithm implements ReplaceAlgorithmInterface
{
    public function execute(string $image, string $imagePath): string
    {
        $replaceImagePath = $this->getReplaceImagePath($imagePath);

        return str_replace($imagePath, $replaceImagePath, $image);
    }

    public function getReplaceImagePath(string $imagePath): string
    {
        return 'custom-replace';
    }

    public function getAlgorithmName(): string
    {
        return 'test';
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function canOverride(): bool
    {
        return true;
    }
}
