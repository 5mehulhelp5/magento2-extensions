<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Page Speed Optimizer Base for Magento 2
 */

namespace Amasty\PageSpeedOptimizer\Model\Output;

use Amasty\PageSpeedOptimizer\Model\Asset;
use Amasty\PageSpeedOptimizer\Model\ConfigProvider;
use Amasty\PageSpeedTools\Model\Output\OutputProcessorInterface;

class AssetCollectorProcessor implements OutputProcessorInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Asset\CollectorAdapter
     */
    private $collectorAdapter;

    public function __construct(
        ConfigProvider $configProvider,
        Asset\CollectorAdapter $collectorAdapter
    ) {
        $this->configProvider = $configProvider;
        $this->collectorAdapter = $collectorAdapter;
    }

    public function process(string &$output): bool
    {
        if (!$this->configProvider->getServerPushType()) {
            return true;
        }

        foreach ($this->configProvider->getServerPushAssetTypes() as $assetType) {
            $collector = $this->collectorAdapter->get($assetType);
            $collector->execute($output);
        }

        return true;
    }
}
