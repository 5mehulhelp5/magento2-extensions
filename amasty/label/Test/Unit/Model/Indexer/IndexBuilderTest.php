<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Test\Unit\Model\Indexer;

use Amasty\Label\Model\Indexer\CacheContext;
use Amasty\Label\Model\Indexer\IndexBuilder;
use Amasty\Label\Model\Label\GetMatchedProductIdsInterface;
use Amasty\Label\Model\ResourceModel\Indexer\ProductTypeDataProvider;
use Amasty\Label\Model\ResourceModel\Label\CollectionFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\ManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionException;
use ReflectionObject;

class IndexBuilderTest extends TestCase
{
    /**
     * @var IndexBuilder
     */
    private $model;

    protected function setUp(): void
    {
        $this->model = new IndexBuilder(
            $this->createMock(ResourceConnection::class),
            $this->createMock(LoggerInterface::class),
            $this->createMock(CollectionFactory::class),
            $this->createMock(ProductRepository::class),
            $this->createMock(ProductCollectionFactory::class),
            $this->createMock(CacheContext::class),
            $this->createMock(ManagerInterface::class),
            $this->createMock(GetMatchedProductIdsInterface::class),
            $this->createMock(ProductTypeDataProvider::class)
        );
    }

    /**
     * @covers IndexBuilder::getTargetStatusId
     *
     * @dataProvider getChangedProductIdsDataProvider
     *
     * @throws ReflectionException
     */
    public function testGetChangedProductIds(
        array $oldLabelIds,
        array $newLabelIds,
        array $expectedChangedProductIds
    ): void {
        $reflectionModel = new ReflectionObject($this->model);
        $testMethod = $reflectionModel->getMethod('getChangedProductIds');
        $testMethod->setAccessible(true);
        $changedProductIds = $testMethod->invoke($this->model, $oldLabelIds, $newLabelIds);

        $this->assertEquals($expectedChangedProductIds, $changedProductIds);
    }

    public function getChangedProductIdsDataProvider(): array
    {
        return [
            [
                [
                    1 => '1,2',
                    2 => '1,3'
                ],
                [
                    1 => '1,2',
                    2 => '1,3'
                ],
                []
            ],
            [
                [
                    1 => '1,2',
                    2 => '1,3'
                ],
                [
                    1 => '1',
                    2 => '1,3'
                ],
                [1]
            ],
            [
                [
                    1 => '2',
                    2 => '3'
                ],
                [
                    1 => '1',
                    2 => '1,3'
                ],
                [1,2]
            ],
            [
                [
                    1 => '2',
                    2 => '3'
                ],
                [
                    1 => '2'
                ],
                [2]
            ],
            [
                [
                    1 => '1,2,3',
                    2 => '3',
                    3 => '6,7'
                ],
                [
                    1 => '2,3',
                    2 => '3',
                    3 => '4,5'
                ],
                [1,3]
            ]
        ];
    }
}
