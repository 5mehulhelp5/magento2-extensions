<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\DataProvider\Config;

use Amasty\MegaMenuLite\Model\ConfigProvider;
use Amasty\MegaMenuLite\Model\Di\Wrapper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Lite implements ArgumentInterface
{
    public const SUBMENU_BACKGROUND_IMAGE = 'submenu_background_image';

    public const SUBMENU_BACKGROUND_IMAGE_PATH = 'amasty/megamenu/submenu_background_image/';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Wrapper
     */
    private $invitationHelper;

    public function __construct(
        ConfigProvider $configProvider,
        UrlInterface $urlBuilder,
        Wrapper $invitationHelper
    ) {
        $this->configProvider = $configProvider;
        $this->urlBuilder = $urlBuilder;
        $this->invitationHelper = $invitationHelper;
    }

    public function modifyConfig(array &$config): void
    {
        $config['is_hamburger'] = $this->configProvider->isHamburgerEnabled();
        $config['color_settings'] =  $this->getColorSettings();
        $config['mobile_menu_title'] = $this->configProvider->getMobileMenuTitle();
        $config['mobile_menu_width'] = $this->configProvider->getMobileMenuWidth();
        $config['welcome_message']['message'] = $this->configProvider->getWelcomeMessage();
        $config['invitation_url'] = (string) $this->invitationHelper->getCustomerInvitationFormUrl();
        $config['hide_view_all_link'] = false;
        $config['mobile_class'] = 'accordion';
    }

    private function getColorSettings(): array
    {
        if ($this->configProvider->isSomeTemplateApplied()) {
            $colorSettings = $this->configProvider->getColorSettings();
            $colorSettings[self::SUBMENU_BACKGROUND_IMAGE] = $this->getSubmenuBackgroundImage();
        }

        return $colorSettings ?? [];
    }

    public function getSubmenuBackgroundImage(): string
    {
        $mediaUrl = $this->urlBuilder->getBaseUrl(['_type' => 'media']) . self::SUBMENU_BACKGROUND_IMAGE_PATH;
        $image = $this->configProvider->getSubmenuBackgroundImage();

        return $image ? $mediaUrl . $image : '';
    }
}
