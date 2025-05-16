<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Test\Unit\Block;

use Amasty\AdvancedReview\Block\Summary;
use Amasty\AdvancedReview\Model\ResourceModel\Review\Collection as ReviewCollection;
use Amasty\AdvancedReview\Test\Unit\Traits;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class SummaryTest
 *
 * @see Summary
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class SummaryTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers Summary::getReviewsCollection
     */
    public function testGetReviewsCollection()
    {
        $block = $this->createPartialMock(Summary::class, []);
        $reviewCollection = $this->createPartialMock(
            ReviewCollection::class,
            ['addStoreFilter', 'addStatusFilter', 'addEntityFilter', 'setDateOrder']
        );
        $reviewCollectionFactory = $this->createMock(CollectionFactory::class);
        $reviewCollectionFactory->expects($this->any())->method('create')->willReturn($reviewCollection);
        $storeManagerMock = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $storeMock = $this->createMock(\Magento\Store\Api\Data\StoreInterface::class);

        $storeMock->expects($this->any())->method('getId')->willReturn(1);
        $storeManagerMock->expects($this->any())->method('getStore')->willReturn($storeMock);
        $reviewCollection->expects($this->once())->method('addStoreFilter')->willReturn($reviewCollection);
        $reviewCollection->expects($this->once())->method('addStatusFilter')->willReturn($reviewCollection);
        $reviewCollection->expects($this->once())->method('addEntityFilter')->willReturn($reviewCollection);
        $reviewCollection->expects($this->once())->method('setDateOrder')->willReturn($reviewCollection);

        $this->setProperty($block, '_storeManager', $storeManagerMock, Summary::class);
        $this->setProperty($block, 'reviewsColFactory', $reviewCollection, Summary::class);
        $this->setProperty($block, 'product', $storeMock, Summary::class);
        $this->setProperty($block, 'reviewsColFactory', $reviewCollectionFactory, Summary::class);

        $block->getReviewsCollection();

        $this->setProperty($block, 'reviewsCollection', 'test', Summary::class);
        $this->assertEquals('test', $block->getReviewsCollection());
    }

    /**
     * @covers Summary::getRatingSummary
     */
    public function testGetRatingSummary()
    {
        $block = $this->createPartialMock(Summary::class, []);
        $product = $this->getMockBuilder(\Magento\Catalog\Model\Product::class)
            ->setMethods(['getRatingSummary'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $product->expects($this->any())->method('getRatingSummary')->willReturnOnConsecutiveCalls(0, $product, 4);

        $this->setProperty($block, 'product', $product, Summary::class);

        $this->assertNull($block->getRatingSummary());
        $this->assertEquals(4, $block->getRatingSummary());
    }

    /**
     * @covers Summary::getRatingSummaryValue
     */
    public function testGetRatingSummaryValue()
    {
        $block = $this->createPartialMock(Summary::class, ['getRatingSummary']);
        $block->expects($this->any())->method('getRatingSummary')->willReturn(10);

        $this->assertEquals(0.5, $block->getRatingSummaryValue());
    }

    /**
     * @covers Summary::getRecomendedPercent
     */
    public function testGetRecomendedPercent()
    {
        $block = $this->createPartialMock(Summary::class, []);
        $reviewCollection = $this->createPartialMock(
            ReviewCollection::class,
            ['addStoreFilter', 'addStatusFilter', 'addEntityFilter', 'setDateOrder', 'addFieldToFilter', 'getSize']
        );
        $reviewCollectionFactory = $this->createMock(CollectionFactory::class);
        $reviewCollectionFactory->expects($this->any())->method('create')->willReturn($reviewCollection);
        $storeManagerMock = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $storeMock = $this->createMock(\Magento\Store\Api\Data\StoreInterface::class);

        $storeMock->expects($this->any())->method('getId')->willReturn(1);
        $storeManagerMock->expects($this->any())->method('getStore')->willReturn($storeMock);
        $reviewCollection->expects($this->any())->method('addStoreFilter')->willReturn($reviewCollection);
        $reviewCollection->expects($this->any())->method('addStatusFilter')->willReturn($reviewCollection);
        $reviewCollection->expects($this->any())->method('addEntityFilter')->willReturn($reviewCollection);
        $reviewCollection->expects($this->any())->method('addFieldToFilter')->willReturn($reviewCollection);
        $reviewCollection->expects($this->any())->method('getSize')->willReturn(10);

        $this->setProperty($block, '_storeManager', $storeManagerMock, Summary::class);
        $this->setProperty($block, 'reviewsColFactory', $reviewCollection, Summary::class);
        $this->setProperty($block, 'product', $storeMock, Summary::class);
        $this->setProperty($block, 'reviewsColFactory', $reviewCollectionFactory, Summary::class);

        $this->assertEquals(100, $block->getRecomendedPercent());
    }
}
