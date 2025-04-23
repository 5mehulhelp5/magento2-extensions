<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\Menu;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Model\DataProvider\GetItemContentData;
use Amasty\MegaMenu\Model\OptionSource\SubcategoriesPosition;
use Amasty\MegaMenu\Model\OptionSource\SubmenuType;
use Magento\Catalog\Model\Category;
use Magento\Store\Model\Store;

class Subcategory
{
    public const TOP_LEVEL = 2;

    /**
     * @var GetItemContentData
     */
    private $getItemContentData;

    public function __construct(
        GetItemContentData $getItemContentData
    ) {
        $this->getItemContentData = $getItemContentData;
    }

    public function isShowSubcategories(
        ?int $level,
        ?int $entityId,
        int $storeId,
        int $topLevel = self::TOP_LEVEL,
        ?string $type = ItemInterface::CATEGORY_TYPE
    ): bool {
        $subcategoriesPosition = $this->getItemContentData->execute(
            ItemInterface::SUBCATEGORIES_POSITION,
            $entityId,
            $storeId,
            $type
        );

        return $level > $topLevel
            && ($subcategoriesPosition === null || $subcategoriesPosition != SubcategoriesPosition::NOT_SHOW);
    }
}
