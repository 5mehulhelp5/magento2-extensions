<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model;

use Amasty\MegaMenuLite\Model\ConfigProvider as ConfigProviderLite;

class ConfigProvider extends ConfigProviderLite
{
    public const STICKY_ENABLED = 'general/sticky';

    public const SHOW_ICONS = 'general/show_icons';

    public const VIEW_ALL_ENABLED = 'general/hide_view_all_link';

    public const MOBILE_TEMPLATE = 'general/mobile_template';

    public const HELP_AND_SETTINGS_DISPLAY = 'general/help_and_settings_display';

    public const  HELP_AND_SETTINGS_TAB_NAME = 'general/help_and_settings_tab_name';

    public function getStickyEnabled(?int $storeId = null): int
    {
        return (int) $this->getValue(self::STICKY_ENABLED, $storeId);
    }

    public function getIconsStatus(): string
    {
        return (string) $this->getValue(self::SHOW_ICONS);
    }

    public function getMobileTemplateClass(?int $storeId = null): ?string
    {
        return (string) $this->getValue(self::MOBILE_TEMPLATE, $storeId);
    }

    public function isHideViewAllLink(?int $storeId = null): ?bool
    {
        return (bool)$this->isSetFlag(self::VIEW_ALL_ENABLED, $storeId);
    }

    public function getHelpAndSettingsDisplay(?int $storeId = null): string
    {
        return $this->getValue(self::HELP_AND_SETTINGS_DISPLAY, $storeId);
    }

    public function getHelpAndSettingsTabName(?int $storeId = null): string
    {
        $settingsValue = (string)$this->getValue(self::HELP_AND_SETTINGS_TAB_NAME, $storeId);

        return empty($settingsValue) ? $this->getDefaultHelpAndSettingsTabName() : $settingsValue;
    }

    public function getDefaultHelpAndSettingsTabName(): string
    {
        return __('Help &amp; Settings')->render();
    }
}
