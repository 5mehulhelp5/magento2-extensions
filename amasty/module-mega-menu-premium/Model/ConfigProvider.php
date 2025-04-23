<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Premium Base for Magento 2
 */

namespace Amasty\MegaMenuPremium\Model;

use Amasty\MegaMenu\Model\ConfigProvider as ConfigProviderPro;

class ConfigProvider extends ConfigProviderPro
{
    public const ANIMATION_TIME = 'general/animation_time';

    public const HAMBURGER_ANIMATION = 'general/hamburger_animation';

    public function getAnimationTime(): ?float
    {
        return (float) $this->getValue(self::ANIMATION_TIME);
    }

    public function getHamburgerAnimation(): ?string
    {
        return $this->getValue(self::HAMBURGER_ANIMATION);
    }
}
