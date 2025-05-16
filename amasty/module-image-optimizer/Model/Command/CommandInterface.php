<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\Command;

use Amasty\ImageOptimizer\Api\Data\QueueInterface;

interface CommandInterface
{
    public function getName(): string;

    public function getType(): string;

    public function run(QueueInterface $queue, string $inputFile, string $outputFile = ''): void;

    public function isAvailable(): bool;

    public function getUnavailableReason(): string;

    public function isVisible(): bool;
}
