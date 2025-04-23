<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Api\Data\Menu;

interface ItemInterface
{
    public const TABLE_NAME = 'amasty_menu_item_content';

    public const ID = 'id';

    public const ENTITY_ID = 'entity_id';

    public const TYPE = 'type';

    public const STORE_ID = 'store_id';

    public const NAME = 'name';

    public const LABEL = 'label';

    public const LABEL_GROUP = 'label_group';

    public const LABEL_TEXT_COLOR = 'label_text_color';

    public const LABEL_BACKGROUND_COLOR = 'label_background_color';

    public const SORT_ORDER = 'sort_order';

    public const CATEGORY_TYPE = 'category';

    public const CUSTOM_TYPE = 'custom';

    public const STATUS = 'status';

    public const USE_DEFAULT = 'use_default';

    public const LINK_TYPE = 'link_type';

    public const LINK = 'link';

    public const SEPARATOR = ', ';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return void
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     *
     * @return void
     */
    public function setEntityId($entityId);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     *
     * @return void
     */
    public function setType($type);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return void
     */
    public function setStoreId($storeId);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return void
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     *
     * @return void
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getLabelTextColor();

    /**
     * @param string $labelColor
     *
     * @return void
     */
    public function setLabelTextColor($labelColor);

    /**
     * @return string
     */
    public function getLabelBackgroundColor();

    /**
     * @param string $labelColor
     *
     * @return void
     */
    public function setLabelBackgroundColor($labelColor);

    /**
     * @return int|null
     */
    public function getStatus();

    /**
     * @param int|null $status
     *
     * @return void
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getLink();

    /**
     * @param string $link
     *
     * @return self
     */
    public function setLink($link);

    /**
     * @return int
     */
    public function getLinkType(): int;

    /**
     * @param int $linkType
     * @return void
     */
    public function setLinkType(int $linkType);

    /**
     * @return string|null
     */
    public function getUseDefault(): ?string;

    /**
     * @param string|null $useDefault
     *
     * @return void
     */
    public function setUseDefault(?string $useDefault): void;
}
