<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\ViewModel\Store;

use Amasty\MegaMenuLite\Model\ConfigProvider;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Menu implements ArgumentInterface
{
    public const PNG = 'png';

    public const ICON_EXTENSION = 'icon_extension';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Json
     */
    private $json;

    public function __construct(
        ConfigProvider $configProvider,
        Json $json
    ) {
        $this->configProvider = $configProvider;
        $this->json = $json;
    }

    public function serialize(array $data): ?string
    {
        return $this->json->serialize($data);
    }

    public function getColorSettings(): array
    {
        return $this->configProvider->getColorSettings();
    }

    public function isSomeTemplateApplied(): bool
    {
        return $this->configProvider->isSomeTemplateApplied();
    }

    public function isPngExtension(array $item): bool
    {
        return $item[self::ICON_EXTENSION] === self::PNG;
    }

    public function isHamburger(): bool
    {
        return $this->configProvider->isHamburgerEnabled();
    }

    public function getMobileMenuWidth(): int
    {
        return $this->configProvider->getMobileMenuWidth();
    }
}
