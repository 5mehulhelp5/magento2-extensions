<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\DataProvider\Config;

use Amasty\MegaMenu\Model\ConfigProvider;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class MegaMenu implements ArgumentInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    public function modifyConfig(array &$config): void
    {
        $config['is_sticky'] = $this->configProvider->getStickyEnabled();
        $config['mobile_class'] = $this->configProvider->getMobileTemplateClass();
        $config['hide_view_all_link'] = $this->configProvider->isHideViewAllLink();
        $config['is_icons_available'] = $this->configProvider->getIconsStatus();
    }
}
