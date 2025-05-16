<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\Command;

class GifCwebp extends Cwebp
{
    public const GIFCWEBP_TYPE = 'gifcwebp';

    public function getType(): string
    {
        return self::GIFCWEBP_TYPE;
    }

    protected function getCommand(): string
    {
        return 'gif2webp -q ' . $this->configProvider->getWebpCompressionQuality() . ' %s -o %s';
    }

    protected function getCheckCommand(): ?string
    {
        return 'gif2webp -h';
    }

    protected function getCheckResult(): ?string
    {
        return 'gif2webp [options]';
    }

    public function isVisible(): bool
    {
        return false;
    }
}
