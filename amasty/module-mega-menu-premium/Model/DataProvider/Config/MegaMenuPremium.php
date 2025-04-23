<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Premium Base for Magento 2
 */

namespace Amasty\MegaMenuPremium\Model\DataProvider\Config;

use Amasty\MegaMenuPremium\Model\ConfigProvider;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class MegaMenuPremium implements ArgumentInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    public function modifyConfig(array &$config): void
    {
        $config['animation_time'] = $this->configProvider->getAnimationTime();
        $config['hamburger_animation'] = $this->configProvider->getHamburgerAnimation();
    }
}
