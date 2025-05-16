<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\ImageProcessor\ResolutionToolProcessor;

use Amasty\ImageOptimizer\Api\Data\QueueInterface;

interface ToolInterface
{
    public function process(QueueInterface $queue, string $newName): void;

    public function getToolName(): string;
}
