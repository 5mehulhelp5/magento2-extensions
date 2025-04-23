<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Test\Unit\Ui\DataProvider\Form\Category\Modifier;

use Amasty\MegaMenu\Model\Backend\Ui\HideField;
use Amasty\MegaMenu\Model\Backend\Ui\HideMobileFieldset;
use Amasty\MegaMenu\Model\Menu\Subcategory;
use Amasty\MegaMenu\Model\OptionSource\SubcategoriesPosition;
use Amasty\MegaMenu\Ui\DataProvider\Form\Category\Modifier\Level;
use Amasty\MegaMenuLite\Model\Provider\FieldsByStore;
use Magento\Catalog\Model\Category;
use Amasty\MegaMenuLite\Test\Unit\Traits;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class LevelTest
 * test Level DataProvider
 *
 * @see Level
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LevelTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers Level::modifyMeta
     *
     * @dataProvider modifyMetaDataProvider
     *
     * @throws \ReflectionException
     */
    public function testModifyMeta(
        array $meta,
        int $parentId,
        int $categoryLevel,
        int $parentCategoryLevel,
        bool $isObjectNew,
        bool $isShowSubcategories,
        array $getFieldsByStore,
        array $expectedResult
    ): void {
        $subcategoriesPosition = $this->createPartialMock(SubcategoriesPosition::class, []);

        $parentCategory = $this->createPartialMock(Category::class, ['getLevel', 'getStoreId']);
        $parentCategory->expects($this->any())->method('getLevel')->willReturn($parentCategoryLevel);
        $parentCategory->expects($this->any())->method('getStoreId')->willReturn(null);

        $entity = $this->createPartialMock(
            Category::class,
            ['getLevel', 'isObjectNew', 'getParentCategory', 'getStoreId']
        );
        $entity->expects($this->any())->method('isObjectNew')->willReturn($isObjectNew);
        $entity->expects($this->any())->method('getLevel')->willReturn($categoryLevel);
        $entity->expects($this->any())->method('getParentCategory')->willReturn($parentCategory);
        $entity->expects($this->any())->method('getStoreId')->willReturn(null);

        $subcategory = $this->createPartialMock(Subcategory::class, ['isShowSubcategories']);
        $subcategory->expects($this->any())->method('isShowSubcategories')->willReturn($isShowSubcategories);

        $fieldsByStore = $this->createPartialMock(FieldsByStore::class, ['getCategoryFields']);
        $fieldsByStore->expects($this->any())->method('getCategoryFields')->willReturn($getFieldsByStore);

        $hideField = $this->createPartialMock(HideField::class, []);
        $arrayManager = $this->createPartialMock(ArrayManager::class, []);
        $this->setProperty($hideField, 'arrayManager', $arrayManager, HideField::class);

        $hideMobileFieldset = $this->createPartialMock(HideMobileFieldset::class, ['execute']);
        $hideMobileFieldset->expects($this->any())->method('execute')->willReturn($meta);

        $level = $this->createPartialMock(Level::class, []);
        $this->setProperty($level, 'entity', $entity, Level::class);
        $this->setProperty($level, 'subcategoriesPosition', $subcategoriesPosition, Level::class);
        $this->setProperty($level, 'subcategory', $subcategory, Level::class);
        $this->setProperty($level, 'parentId', $parentId, Level::class);
        $this->setProperty($level, 'fieldsByStore', $fieldsByStore, Level::class);
        $this->setProperty($level, 'hideField', $hideField, Level::class);
        $this->setProperty($level, 'hideMobileFieldset', $hideMobileFieldset, Level::class);

        $actualResult = $level->modifyMeta($meta);

        $this->assertEquals($expectedResult, $this->prepareData($actualResult));
    }

    private function prepareData(array $actualResult): array
    {
        $fields = $actualResult['am_mega_menu_fieldset']['children'];
        $subcategories_position = array_column(
            $fields['subcategories_position']['arguments']['data']['options'],
            'value'
        );

        $data['submenu_type'] = $fields['submenu_type']['arguments']['data']['config']['switcherConfig']['enabled'];
        $data['subcategories_position'] = $subcategories_position;

        $config = $fields['content']['arguments']['data']['config'] ?? null;
        if (isset($config['notice']) && isset($config['default'])) {
            $data['notice'] = $config['notice'];
            $data['default'] = $config['default'];
        }

        return $data;
    }

    /**
     * Data provider for modifyMeta test
     * @return array
     */
    public function modifyMetaDataProvider(): array
    {
        $config['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config']['notice'] = 1;
        $config['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config']['default'] = 1;

        return [
            [
                $config,
                2,
                1,
                0,
                true,
                true,
                ['am_mega_menu_fieldset' => []],
                [
                    'submenu_type' => false,
                    'subcategories_position' => [1, 2],
                    'notice' => 1,
                    'default' => 1
                ]
            ],
            [
                $config,
                0,
                1,
                0,
                false,
                true,
                ['am_mega_menu_fieldset' => []],
                [
                    'submenu_type' => false,
                    'subcategories_position' => [1, 2],
                    'notice' => 1,
                    'default' => 1
                ]
            ],
            [
                $config,
                2,
                2,
                1,
                true,
                true,
                ['am_mega_menu_fieldset' => []],
                [
                    'submenu_type' => true,
                    'subcategories_position' => [1, 2]
                ]
            ],
            [
                $config,
                2,
                2,
                1,
                false,
                false,
                ['am_mega_menu_fieldset' => []],
                [
                    'submenu_type' => true,
                    'subcategories_position' => [1, 2],
                    'notice' => 1,
                    'default' => 1
                ]
            ],
            [
                $config,
                2,
                2,
                2,
                true,
                true,
                ['am_mega_menu_fieldset' => []],
                [
                    'submenu_type' => false,
                    'subcategories_position' => [1, 2, 0],
                    'notice' => 1,
                    'default' => 1
                ]
            ],
            [
                $config,
                2,
                3,
                1,
                false,
                false,
                ['am_mega_menu_fieldset' => []],
                [
                    'submenu_type' => false,
                    'subcategories_position' => [1, 2, 0],
                    'notice' => 1,
                    'default' => 1
                ]
            ]
        ];
    }
}
