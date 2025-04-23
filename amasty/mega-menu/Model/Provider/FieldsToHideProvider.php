<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\Provider;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Model\Menu\Subcategory;
use Magento\Store\Model\Store;

class FieldsToHideProvider
{
    public const CATEGORY_LEVEL_ERROR = 'category_level_error';

    /**
     * @var Subcategory
     */
    private $subcategory;

    public function __construct(
        Subcategory $subcategory
    ) {
        $this->subcategory = $subcategory;
    }

    public function getRootCategoryFields(): array
    {
        return [
            ItemInterface::WIDTH,
            ItemInterface::WIDTH_VALUE,
            ItemInterface::COLUMN_COUNT,
            ItemInterface::LABEL,
            ItemInterface::LABEL_GROUP,
            ItemInterface::CONTENT,
            ItemInterface::HIDE_CONTENT,
            ItemInterface::SUBMENU_TYPE,
            ItemInterface::SUBCATEGORIES_POSITION,
            ItemInterface::ICON
        ];
    }

    public function getMainCategoryFields(): array
    {
        return [self::CATEGORY_LEVEL_ERROR];
    }

    public function getSubcategoryFields(
        ?int $level,
        ?int $entityId,
        int $storeId,
        int $topLevel = Subcategory::TOP_LEVEL,
        string $type = ItemInterface::CATEGORY_TYPE
    ): array {
        $isShowSubcategories = $this->subcategory->isShowSubcategories($level, $entityId, $storeId, $topLevel, $type);
        $itemsToHide = $this->getSubcategoryItems($type, $isShowSubcategories);

        return $itemsToHide;
    }

    private function getSubcategoryItems(string $type, bool $isShowSubcategories = false): array
    {
        if ($isShowSubcategories) {
            $itemsToHide = [
                ItemInterface::WIDTH,
                ItemInterface::WIDTH_VALUE,
                ItemInterface::COLUMN_COUNT,
                ItemInterface::SUBMENU_TYPE
            ];
            if ($type === ItemInterface::CATEGORY_TYPE) {
                $itemsToHide[] = self::CATEGORY_LEVEL_ERROR;
            }
        } else {
            $itemsToHide = [
                ItemInterface::WIDTH,
                ItemInterface::WIDTH_VALUE,
                ItemInterface::COLUMN_COUNT,
                ItemInterface::CONTENT,
                ItemInterface::SUBMENU_TYPE,
                ItemInterface::SUBCATEGORIES_POSITION
            ];
        }

        return $itemsToHide;
    }
}
