<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\Image\Directory;

interface FileSelectorInterface
{
    public function selectFiles(array $files, string $imageDirectory): array;
}
