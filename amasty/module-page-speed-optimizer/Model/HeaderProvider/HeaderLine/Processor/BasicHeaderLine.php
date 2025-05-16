<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Page Speed Optimizer Base for Magento 2
 */

namespace Amasty\PageSpeedOptimizer\Model\HeaderProvider\HeaderLine\Processor;

use Amasty\PageSpeedOptimizer\Model\Asset\CollectorAdapter;
use Amasty\PageSpeedOptimizer\Model\ConfigProvider;
use Amasty\PageSpeedTools\Model\Asset\AssetCollectorInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class BasicHeaderLine implements HeaderLineInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CollectorAdapter
     */
    private $collectorAdapter;

    public function __construct(
        ConfigProvider $configProvider,
        StoreManagerInterface $storeManager,
        CollectorAdapter $collectorAdapter
    ) {
        $this->configProvider = $configProvider;
        $this->storeManager = $storeManager;
        $this->collectorAdapter = $collectorAdapter;
    }

    public function getHeaderLine(): string
    {
        $assets = [];
        $baseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB);
        $assetTypes = $this->configProvider->getServerPushAssetTypes();
        $pushIgnoreList = $this->configProvider->getServerPushIgnoreList();

        /** @var AssetCollectorInterface $collector */
        foreach ($this->collectorAdapter->getByTypes($assetTypes) as $collector) {
            foreach ($collector->getCollectedAssets() as $collectedAssetUrl) {
                if ($this->isAssetUrlIgnored($pushIgnoreList, $collectedAssetUrl)) {
                    continue;
                }

                $assetParts = [
                    sprintf('<%s>', str_replace($baseUrl, '/', $collectedAssetUrl)),
                    'rel=preload',
                    sprintf('as=%s', $collector->getAssetContentType()),
                ];

                if ($collector->getAssetContentType() === 'font') {
                    $assetParts[] = 'crossorigin=anonymous';
                }

                $assets[] = implode('; ', $assetParts);
            }
        }

        return implode(', ', $assets);
    }

    private function isAssetUrlIgnored(array $ignoreList, string $assetUrl): bool
    {
        $isIgnored = false;

        foreach ($ignoreList as $urlPart) {
            if (strpos($assetUrl, $urlPart) !== false) {
                $isIgnored = true;
                break;
            }
        }

        return $isIgnored;
    }
}
