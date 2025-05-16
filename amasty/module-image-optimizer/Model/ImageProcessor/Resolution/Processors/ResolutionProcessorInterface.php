<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\ImageProcessor\Resolution\Processors;

use Amasty\ImageOptimizer\Api\Data\QueueInterface;

interface ResolutionProcessorInterface
{
    public function execute(QueueInterface $queue): void;

    public function getType(): string;
}
