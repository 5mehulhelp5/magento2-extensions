<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Api\Data\Menu;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface as ItemInterfaceLite;

interface ItemInterface extends ItemInterfaceLite
{
    public const CONTENT = 'content';

    public const WIDTH = 'width';

    public const WIDTH_VALUE = 'width_value';

    public const COLUMN_COUNT = 'column_count';

    public const ICON = 'icon';

    public const SUBCATEGORIES_POSITION = 'subcategories_position';

    public const SUBMENU_TYPE = 'submenu_type';

    public const HIDE_CONTENT = 'hide_content';

    public const DESKTOP_FONT = 'desktop_font';

    public const MOBILE_FONT = 'mobile_font';

    /**
     * @return string|null
     */
    public function getContent();

    /**
     * @return int|null
     */
    public function getWidth();

    /**
     * @param int|null $width
     *
     * @return void
     */
    public function setWidth($width);

    /**
     * @return int|null
     */
    public function getWidthValue();

    /**
     * @param int|null $width
     *
     * @return void
     */
    public function setWidthValue($width);

    /**
     * @return int|null
     */
    public function getColumnCount();

    /**
     * @param int|null $columnCount
     *
     * @return void
     */
    public function setColumnCount($columnCount);

    /**
     * @return string|null
     */
    public function getIcon(): ?string;

    /**
     * @param string|null $icon
     *
     * @return void
     */
    public function setIcon($icon);

    /**
     * @return int|null
     */
    public function getSubmenuType(): ?int;

    /**
     * @param int|null $submenuType
     *
     * @return void
     */
    public function setSubmenuType($submenuType);

    /**
     * @return int|null
     */
    public function getSubcategoriesPosition(): ?int;

    /**
     * @param int|null $subcategoriesPosition
     *
     * @return void
     */
    public function setSubcategoriesPosition($subcategoriesPosition);

    /**
     * @return bool|null
     */
    public function isHideContent(): ?bool;

    /**
     * @param bool|null $hideContent
     *
     * @return void
     */
    public function setHideContent($hideContent);

    /**
     * @return int
     */
    public function getDesktopFont(): ?int;

    /**
     * @param int|null $desktopFont
     *
     * @return void
     */
    public function setDesktopFont(?int $desktopFont): void;

    /**
     * @return int
     */
    public function getMobileFont(): ?int;

    /**
     * @param int|null $mobileFont
     *
     * @return void
     */
    public function setMobileFont(?int $mobileFont): void;
}
