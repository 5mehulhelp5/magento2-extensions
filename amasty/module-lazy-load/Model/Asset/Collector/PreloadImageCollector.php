<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Lazy Load for Magento 2 (System)
 */

namespace Amasty\LazyLoad\Model\Asset\Collector;

use Amasty\PageSpeedTools\Model\Asset\AssetCollectorInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class PreloadImageCollector implements AssetCollectorInterface
{
    public const ALLOWED_IMG_EXTENSIONS = ['svg', 'png', 'webp', 'jpeg', 'jpg'];

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var array
     */
    private $collectedAssets = [];

    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    public function getAssetContentType(): string
    {
        return 'image';
    }

    public function getCollectedAssets(): array
    {
        return $this->collectedAssets;
    }

    public function execute(string $output)
    {
        return null;
    }

    public function addImageAsset(string $assetUrl)
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB);

        if (strstr($assetUrl, $baseUrl)) {
            // phpcs:disable Magento2.Functions.DiscouragedFunction.DiscouragedWithAlternative
            $assetUrlExtension = pathinfo(
                // phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
                parse_url($assetUrl, PHP_URL_PATH),
                PATHINFO_EXTENSION
            );
            if (in_array($assetUrlExtension, self::ALLOWED_IMG_EXTENSIONS)) {
                $this->collectedAssets[] = $assetUrl;
            }
        }
    }
}
