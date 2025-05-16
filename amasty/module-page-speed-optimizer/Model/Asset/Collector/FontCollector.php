<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Page Speed Optimizer Base for Magento 2
 */

namespace Amasty\PageSpeedOptimizer\Model\Asset\Collector;

class FontCollector extends AbstractAssetCollector
{
    public const REGEX = '/<link[^>]*href\s*=\s*["|\'](?<asset_url>.*?(eot|fft|otf|woff2|woff))["\']+[^>]*>/i';

    public function getAssetContentType(): string
    {
        return 'font';
    }
}
