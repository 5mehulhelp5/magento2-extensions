<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\ImageProcessor\AutoProcessing\Processors;

interface AutoProcessorInterface
{
    /**
     * @param string $imgPath
     * @return void
     */
    public function execute(string $imgPath): void;
}
