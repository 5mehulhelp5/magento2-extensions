<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Model\Image\ReplaceAlgorithm;

/**
 * Empty algorithm to be used by default if failed to resolve any for replace pattern
 */
class DummyAlgorithm implements ReplaceAlgorithmInterface
{
    public const ALGORITHM_NAME = 'dummy';

    public function execute(string $image, string $imagePath): string
    {
        return $image;
    }

    public function getReplaceImagePath(string $imagePath): string
    {
        return $imagePath;
    }

    public function getAlgorithmName(): string
    {
        return self::ALGORITHM_NAME;
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function canOverride(): bool
    {
        return false;
    }
}
