<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Plugin\Catalog\ViewModel\Product\Breadcrumbs;

use Amasty\MegaMenuLite\Model\ConfigProvider;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\ViewModel\Product\Breadcrumbs as BreadcrumbsViewModel;
use Magento\Framework\Serialize\Serializer\JsonHexTag;

class FixSelector
{
    /**
     * @var JsonHexTag
     */
    private $jsonSerializer;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        JsonHexTag $jsonSerializer,
        ConfigProvider $configProvider = null //todo: move to not optional
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->configProvider = $configProvider ?? ObjectManager::getInstance()->get(ConfigProvider::class);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetJsonConfigurationHtmlEscaped(
        BreadcrumbsViewModel $subject,
        string $breadcrumbsConfig
    ): string {
        if (!$this->configProvider->isEnabled()) {
            return $breadcrumbsConfig;
        }

        $breadcrumbsConfigArray = $this->jsonSerializer->unserialize($breadcrumbsConfig);
        if (!isset($breadcrumbsConfigArray['breadcrumbs'])) {
            return $breadcrumbsConfig;
        }

        $breadcrumbsConfigArray['breadcrumbs']['menuContainer']
            = '.ammenu-robots-navigation [data-action="navigation"] > ul';

        return $this->jsonSerializer->serialize($breadcrumbsConfigArray);
    }
}
