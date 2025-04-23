<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Api\Data\Menu;

interface LinkInterface
{
    public const TABLE_NAME = 'amasty_menu_link';

    public const PERSIST_NAME = 'amasty_megamenu_link';

    public const ENTITY_ID = 'entity_id';
    /**
     * @deprecated
     */
    public const LINK = 'link';
    /**
     * @deprecated
     */
    public const TYPE = 'link_type';
    public const PARENT_ID = 'parent_id';
    public const PATH = 'path';
    public const LEVEL = 'level';

    public const DEFAULT_LEVEL = 0;
    public const DEFAULT_PATH = '0/';
    public const PATH_SEPARATOR = '/';
    public const LEVEL_STEP = 1;

    /**
     * @return int
     */
    public function getEntityId(): int;

    /**
     * @param int $entityId
     *
     * @return \Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface
     */
    public function setEntityId($entityId);

    /**
     * @return string
     * @deprecated
     */
    public function getLink();

    /**
     * @param string $link
     *
     * @return self
     * @deprecated
     */
    public function setLink($link);

    /**
     * @return int
     * @deprecated
     */
    public function getLinkType(): int;

    /**
     * @param int $linkType
     * @return void
     * @deprecated
     */
    public function setLinkType(int $linkType);

    /**
     * @return int|null
     */
    public function getParentId(): int;

    /**
     * @param int|null $parentId
     *
     * @return void
     */
    public function setParentId(?int $parentId): void;

    /**
     * @return string|null
     */
    public function getPath(): ?string;

    /**
     * @param string|null $path
     *
     * @return void
     */
    public function setPath(?string $path): void;

    /**
     * @return int
     */
    public function getLevel(): int;

    /**
     * @param int $level
     *
     * @return void
     */
    public function setLevel(int $level): void;
}
