<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Model\Image;

use Amasty\PageSpeedTools\Model\Image\ReplacePatterns\ReplaceConfigInterface;

interface ReplacerCompositeInterface
{
    public function replace(ReplaceConfigInterface $replaceConfig, string $image, string $imagePath): string;

    public function replaceImagePath(ReplaceConfigInterface $replaceConfig, string $imagePath): string;
}
