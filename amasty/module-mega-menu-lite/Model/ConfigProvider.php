<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model;

use Amasty\Base\Model\ConfigProviderAbstract;
use Amasty\MegaMenuLite\Model\OptionSource\ColorTemplate;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider extends ConfigProviderAbstract
{
    public const ENABLED = 'general/enabled';

    public const HAMBURGER_ENABLED = 'general/hamburger_enabled';

    public const SUBMENU_BACKGROUND_IMAGE = 'color/submenu_background_image';

    public const MENU_BACKGROUND_COLOR = 'color/main_menu_background';

    public const COLOR_TEMPLATE = 'color/color_template';

    public const COLOR = 'color';

    public const DESIGN_HEADER_WELCOME = 'design/header/welcome';

    public const DESIGN_HEADER_LOGO_SRC = 'design/header/logo_src';

    public const MOBILE_MENU_TITLE = 'general/mobile_menu_title';

    public const MOBILE_MENU_WIDTH = 'general/mobile_menu_width';
    public const MOBILE_MENU_WIDTH_DEFAULT = 1024;

    /**
     * @var string
     */
    protected $pathPrefix = 'ammegamenu/';

    public function isEnabled(?int $storeId = null): ?bool
    {
        return (bool)$this->isSetFlag(self::ENABLED, $storeId);
    }

    public function isHamburgerEnabled(?int $storeId = null): ?bool
    {
        return (bool)$this->isSetFlag(self::HAMBURGER_ENABLED, $storeId);
    }

    public function getColorTemplate(): ?string
    {
        return (string)$this->getValue(self::COLOR_TEMPLATE);
    }

    public function isSomeTemplateApplied(): bool
    {
        return $this->getColorTemplate() !== ColorTemplate::BLANK;
    }

    public function getColorSettings(): array
    {
        return $this->getValue(self::COLOR);
    }

    public function getWelcomeMessage(): ?string
    {
        return $this->scopeConfig->getValue(self::DESIGN_HEADER_WELCOME, ScopeInterface::SCOPE_STORE);
    }

    public function getHeaderLogoSrc(): ?string
    {
        return $this->scopeConfig->getValue(self::DESIGN_HEADER_LOGO_SRC, ScopeInterface::SCOPE_STORE);
    }

    public function getSubmenuBackgroundImage(): ?string
    {
        return $this->getValue(self::SUBMENU_BACKGROUND_IMAGE);
    }

    public function getMobileMenuTitle(): ?string
    {
        return (string) $this->getValue(self::MOBILE_MENU_TITLE);
    }

    public function getMobileMenuWidth(): int
    {
        return (int) $this->getValue(self::MOBILE_MENU_WIDTH) ?: self::MOBILE_MENU_WIDTH_DEFAULT;
    }
}
