<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

// @codingStandardsIgnoreFile

declare(strict_types=1);

namespace Amasty\MegaMenu\Test\Unit\Model\Menu;

use Amasty\MegaMenu\Model\DataProvider\GetItemContentData;
use Amasty\MegaMenu\Model\Menu\Subcategory;
use Amasty\MegaMenu\Model\OptionSource\SubcategoriesPosition;
use Amasty\MegaMenu\Model\OptionSource\SubmenuType;
use Amasty\MegaMenuLite\Test\Unit\Traits;
use Magento\Catalog\Model\Category;
use Magento\Store\Model\Store;

/**
 * Class SubcategoryTest
 * test Subcategory DataProvider
 *
 * @see Subcategory
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SubcategoryTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var Subcategory
     */
    private $model;

    protected function setup(): void
    {
        $getItemContentData = $this->createMock(GetItemContentData::class);
        $this->model = new Subcategory($getItemContentData);
    }


    /**
     * @covers Subcategory::isShowSubcategories
     *
     * @dataProvider isShowSubcategoriesDataProvider
     *
     * @throws \ReflectionException
     */
    public function testIsShowSubcategories(
        ?int $subcategoriesPosition,
        int $entityId,
        int $level,
        int $storeId,
        bool $expectedResult
    ): void {
        $getItemContentData = $this->getProperty($this->model, 'getItemContentData');
        $getItemContentData->expects($this->any())->method('execute')->willReturnOnConsecutiveCalls($subcategoriesPosition);
        $actualResult = $this->model->isShowSubcategories($level, $entityId, $storeId);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * Data provider for isShowSubcategories test
     * @return array
     */
    public function isShowSubcategoriesDataProvider(): array
    {
        return [
            [
                SubcategoriesPosition::LEFT,
                1,
                Subcategory::TOP_LEVEL,
                Store::DEFAULT_STORE_ID,
                false
            ],
            [
                SubcategoriesPosition::LEFT,
                1,
                3,
                Store::DEFAULT_STORE_ID,
                true
            ],
            [
                SubcategoriesPosition::NOT_SHOW,
                1,
                3,
                Store::DEFAULT_STORE_ID,
                false
            ]
        ];
    }
}
