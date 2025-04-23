<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Premium Base for Magento 2
 */

namespace Amasty\MegaMenuPremium\Api\Data\Menu;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface as ItemInterfacePro;

interface ItemInterface extends ItemInterfacePro
{
    public const MOBILE_CONTENT = 'mobile_content';

    public const SHOW_MOBILE_CONTENT = 'show_mobile_content';

    public const HIDE_MOBILE_CONTENT = 'hide_mobile_content';

    public const SUBMENU_ANIMATION = 'submenu_animation';

    /**
     * @return string|null
     */
    public function getMobileContent();

    /**
     * @param string|null $mobileContent
     *
     * @return void
     */
    public function setMobileContent($mobileContent);

    /**
     * @return int|null
     */
    public function getShowMobileContent();

    /**
     * @param int|null $showMobileContent
     *
     * @return void
     */
    public function setShowMobileContent($showMobileContent);

    /**
     * @return bool|null
     */
    public function isHideMobileContent(): ?bool;

    /**
     * @param bool|null $hideMobileContent
     *
     * @return void
     */
    public function setHideMobileContent($hideMobileContent);

    /**
     * @return string|null
     */
    public function getSubmenuAnimation();

    /**
     * @param string|null $submenuAnimation
     *
     * @return void
     */
    public function setSubmenuAnimation($submenuAnimation);
}
