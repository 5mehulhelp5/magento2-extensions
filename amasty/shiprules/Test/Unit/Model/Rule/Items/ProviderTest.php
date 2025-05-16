<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Test\Unit\Model\Rule\Items;

use Amasty\CommonRules\Model\Rule\Condition\Combine;
use Amasty\Shiprules\Model\Rule;
use Amasty\Shiprules\Model\Rule\Items\Provider;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProductType;
use Magento\Quote\Model\Quote\Item;
use PHPUnit\Framework\TestCase;

class ProviderTest extends TestCase
{
    /**
     * @var Provider
     */
    private $subject;

    public function setUp(): void
    {
        $this->subject = new Provider();
    }

    /**
     * @param array $items
     * @param array $validItems
     * @dataProvider getValidItemsProvider
     */
    public function testGetValidItems(array $items, array $validItems): void
    {
        $ruleId = 1;
        $actionsMock = $this->createMock(Combine::class);
        $ruleMock = $this->createConfiguredMock(
            Rule::class,
            [
                'getRuleId' => $ruleId,
                'getActions' => $actionsMock
            ]
        );

        $actionsMock->expects($this->atLeastOnce())
            ->method('validate')
            ->willReturn(true);

        $this->assertEquals($validItems, $this->subject->getValidItems($ruleMock, $items));
    }

    /**
     * @param array $items
     * @param array $itemIds
     * @dataProvider getAllItemIdsProvider
     */
    public function testGetAllItemIds(array $items, array $itemIds): void
    {
        $this->assertEquals($itemIds, $this->subject->getAllItemIds($items));
    }

    /**
     * @return array
     */
    public function getValidItemsProvider(): array
    {
        $configurableProductMock = $this->createConfiguredMock(
            ProductInterface::class,
            ['getTypeId' => ConfigurableProductType::TYPE_CODE]
        );
        $simpleProductMock = $this->createConfiguredMock(
            ProductInterface::class,
            ['getTypeId' => Type::TYPE_SIMPLE]
        );
        $simpleMock = $this->createConfiguredMock(
            Item::class,
            [
                'getProduct' => $simpleProductMock,
                'getId' => 1
            ]
        );
        $configurableChildMock = $this->createMock(Item::class);
        $configurableMock = $this->createConfiguredMock(
            Item::class,
            [
                'getProduct' => $configurableProductMock,
                'getChildren' => [$configurableChildMock],
                'getId' => 1
            ]
        );

        $configurableChildMock->expects($this->once())
            ->method('getParentItem')
            ->willReturn($configurableMock);

        return [
            [[$configurableMock, $configurableChildMock], [1 => $configurableMock]],
            [[$simpleMock], [1 => $simpleMock]]
        ];
    }

    /**
     * @return array
     */
    public function getAllItemIdsProvider(): array
    {
        $simpleMock = $this->createConfiguredMock(Item::class, ['getId' => 1]);
        $configurableMock = $this->createConfiguredMock(Item::class, ['getId' => 1]);
        $configurableChildMock = $this->createConfiguredMock(
            Item::class,
            ['getParentItem' => $configurableMock]
        );

        return [
            [[$configurableMock, $configurableChildMock], [1]],
            [[$simpleMock], [1]]
        ];
    }
}
