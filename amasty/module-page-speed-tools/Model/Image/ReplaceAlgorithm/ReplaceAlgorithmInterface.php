<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Model\Image\ReplaceAlgorithm;

interface ReplaceAlgorithmInterface
{
    public function execute(string $image, string $imagePath): string;

    public function getReplaceImagePath(string $imagePath): string;

    public function getAlgorithmName(): string;

    public function isAvailable(): bool;

    public function canOverride(): bool;
}
