<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

// @codingStandardsIgnoreFile

declare(strict_types=1);

namespace Amasty\MegaMenu\Test\Unit\Ui\DataProvider\Form\Category\Modifier;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Model\Backend\Ui\HideField;
use Amasty\MegaMenu\Model\Menu\Subcategory;
use Amasty\MegaMenu\Model\Provider\FieldsToHideProvider;
use Amasty\MegaMenuLite\Test\Unit\Traits;
use Amasty\MegaMenu\Ui\DataProvider\Form\Category\Modifier\HideItems;
use Magento\Catalog\Model\Category;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class HideItemsTest
 * test HideItems DataProvider
 *
 * @see HideItems
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class HideItemsTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers HideItems::modifyMeta
     *
     * @dataProvider modifyMetaDataProvider
     *
     * @throws \ReflectionException
     */
    public function testModifyMeta(
        array $meta,
        int $parentId,
        int $level,
        bool $hasChildren,
        bool $isObjectNew,
        bool $isShowSubcategories,
        array $expectedResult
    ): void {
        $entity = $this->createPartialMock(Category::class, ['getLevel', 'hasChildren', 'isObjectNew', 'getParentCategory']);
        $entity->expects($this->any())->method('getLevel')->willReturn($level);
        $entity->expects($this->any())->method('hasChildren')->willReturn($hasChildren);
        $entity->expects($this->any())->method('isObjectNew')->willReturn($isObjectNew);
        $entity->expects($this->any())->method('getParentCategory')->willReturn($this->createMock(Category::class));

        $subcategory = $this->createPartialMock(Subcategory::class, ['isShowSubcategories']);
        $subcategory->expects($this->any())->method('isShowSubcategories')->willReturn($isShowSubcategories);

        $fieldsToHideProvider = $this->createPartialMock(FieldsToHideProvider::class, []);
        $this->setProperty($fieldsToHideProvider, 'subcategory', $subcategory, FieldsToHideProvider::class);

        $hideField = $this->createPartialMock(HideField::class, []);
        $arrayManager = $this->createPartialMock(ArrayManager::class, []);
        $this->setProperty($hideField, 'arrayManager', $arrayManager, HideField::class);

        $hideItems = $this->createPartialMock(HideItems::class, []);
        $this->setProperty($hideItems, 'fieldsToHideProvider', $fieldsToHideProvider, HideItems::class);
        $this->setProperty($hideItems, 'entity', $entity, HideItems::class);
        $this->setProperty($hideItems, 'parentId', $parentId, HideItems::class);
        $this->setProperty($hideItems, 'hideField', $hideField, HideItems::class);

        $actualResult = $hideItems->modifyMeta($meta);

        $this->assertEquals($expectedResult, array_keys($actualResult['am_mega_menu_fieldset']['children']));
    }

    /**
     * Data provider for modifyMeta test
     * @return array
     */
    public function modifyMetaDataProvider(): array
    {
        return [
            [
                [],
                0,
                1,
                true,
                true,
                true,
                [ItemInterface::HIDE_CONTENT, ItemInterface::DESKTOP_FONT, ItemInterface::WIDTH, ItemInterface::WIDTH_VALUE, ItemInterface::COLUMN_COUNT, ItemInterface::LABEL, ItemInterface::LABEL_GROUP, ItemInterface::CONTENT, ItemInterface::SUBMENU_TYPE, ItemInterface::SUBCATEGORIES_POSITION, ItemInterface::ICON]
            ],
            [
                [],
                0,
                1,
                true,
                false,
                false,
                [ItemInterface::HIDE_CONTENT, ItemInterface::WIDTH, ItemInterface::WIDTH_VALUE, ItemInterface::COLUMN_COUNT, ItemInterface::LABEL, ItemInterface::LABEL_GROUP, ItemInterface::CONTENT, ItemInterface::SUBMENU_TYPE, ItemInterface::SUBCATEGORIES_POSITION, ItemInterface::ICON]
            ],
            [
                [],
                0,
                1,
                false,
                true,
                true,
                [ItemInterface::HIDE_CONTENT, ItemInterface::DESKTOP_FONT, ItemInterface::WIDTH, ItemInterface::WIDTH_VALUE, ItemInterface::COLUMN_COUNT, ItemInterface::LABEL, ItemInterface::LABEL_GROUP, ItemInterface::CONTENT, ItemInterface::SUBMENU_TYPE, ItemInterface::SUBCATEGORIES_POSITION, ItemInterface::ICON]
            ],
            [
                [],
                0,
                1,
                false,
                false,
                false,
                [ItemInterface::HIDE_CONTENT, ItemInterface::WIDTH, ItemInterface::WIDTH_VALUE, ItemInterface::COLUMN_COUNT, ItemInterface::LABEL, ItemInterface::LABEL_GROUP, ItemInterface::CONTENT, ItemInterface::SUBMENU_TYPE, ItemInterface::SUBCATEGORIES_POSITION, ItemInterface::ICON]
            ],
            [
                [],
                0,
                2,
                true,
                true,
                true,
                [ItemInterface::DESKTOP_FONT, FieldsToHideProvider::CATEGORY_LEVEL_ERROR, ItemInterface::SUBMENU_TYPE]
            ],
            [
                [],
                0,
                2,
                true,
                false,
                false,
                [FieldsToHideProvider::CATEGORY_LEVEL_ERROR]
            ],
            [
                [],
                0,
                2,
                false,
                true,
                true,
                [ItemInterface::DESKTOP_FONT, FieldsToHideProvider::CATEGORY_LEVEL_ERROR, ItemInterface::SUBCATEGORIES_POSITION, ItemInterface::SUBMENU_TYPE]
            ],
            [
                [],
                0,
                2,
                false,
                false,
                false,
                [FieldsToHideProvider::CATEGORY_LEVEL_ERROR, ItemInterface::SUBCATEGORIES_POSITION, ItemInterface::SUBMENU_TYPE]
            ],
            [
                [],
                0,
                3,
                true,
                true,
                true,
                [ItemInterface::DESKTOP_FONT, ItemInterface::WIDTH, ItemInterface::WIDTH_VALUE, ItemInterface::COLUMN_COUNT, ItemInterface::SUBMENU_TYPE, FieldsToHideProvider::CATEGORY_LEVEL_ERROR]
            ],
            [
                [],
                0,
                3,
                true,
                true,
                false,
                [ItemInterface::DESKTOP_FONT, ItemInterface::WIDTH, ItemInterface::WIDTH_VALUE, ItemInterface::COLUMN_COUNT, ItemInterface::CONTENT, ItemInterface::SUBMENU_TYPE, ItemInterface::SUBCATEGORIES_POSITION]
            ],
            [
                [],
                0,
                3,
                true,
                false,
                true,
                [ItemInterface::WIDTH, ItemInterface::WIDTH_VALUE, ItemInterface::COLUMN_COUNT, ItemInterface::SUBMENU_TYPE, FieldsToHideProvider::CATEGORY_LEVEL_ERROR]
            ],
            [
                [],
                0,
                3,
                true,
                false,
                false,
                [ItemInterface::WIDTH, ItemInterface::WIDTH_VALUE, ItemInterface::COLUMN_COUNT, ItemInterface::CONTENT, ItemInterface::SUBMENU_TYPE, ItemInterface::SUBCATEGORIES_POSITION]
            ],
            [
                [],
                0,
                3,
                false,
                true,
                true,
                [ItemInterface::DESKTOP_FONT, ItemInterface::WIDTH, ItemInterface::WIDTH_VALUE, ItemInterface::COLUMN_COUNT, ItemInterface::SUBMENU_TYPE, FieldsToHideProvider::CATEGORY_LEVEL_ERROR, ItemInterface::SUBCATEGORIES_POSITION]
            ],
            [
                [],
                0,
                3,
                false,
                true,
                false,
                [ItemInterface::DESKTOP_FONT, ItemInterface::WIDTH, ItemInterface::WIDTH_VALUE, ItemInterface::COLUMN_COUNT, ItemInterface::CONTENT, ItemInterface::SUBMENU_TYPE, ItemInterface::SUBCATEGORIES_POSITION]
            ]
            ,
            [
                [],
                0,
                3,
                false,
                false,
                true,
                [ItemInterface::WIDTH, ItemInterface::WIDTH_VALUE, ItemInterface::COLUMN_COUNT, ItemInterface::SUBMENU_TYPE, FieldsToHideProvider::CATEGORY_LEVEL_ERROR, ItemInterface::SUBCATEGORIES_POSITION]
            ]
        ];
    }
}
