<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\ImageProcessor\Resolution;

use Amasty\ImageOptimizer\Api\Data\QueueInterface;
use Amasty\ImageOptimizer\Model\ConfigProvider;

class ResolutionProcessorApplier
{
    /**
     * @var ResolutionProcessorsPool
     */
    private $pool;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        ResolutionProcessorsPool $pool,
        ConfigProvider $configProvider
    ) {
        $this->pool = $pool;
        $this->configProvider = $configProvider;
    }

    public function apply(QueueInterface $queue): void
    {
        $type = $this->configProvider->getResolutionsLib();

        $this->pool->getByType($type)->execute($queue);
    }
}
