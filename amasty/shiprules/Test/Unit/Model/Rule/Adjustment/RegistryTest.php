<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Test\Unit\Model\Rule\Adjustment;

use Amasty\Shiprules\Model\Rule\Adjustment\Registry;
use Amasty\Shiprules\Model\Rule\AdjustmentData;
use Amasty\Shiprules\Model\Rule\AdjustmentDataFactory as Factory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RegistryTest extends TestCase
{
    /**
     * @var Factory|MockObject
     */
    private $adjustmentFactoryMock;

    /**
     * @var Registry
     */
    private $subject;

    public function setUp(): void
    {
        $this->adjustmentFactoryMock = $this->createMock(Factory::class);
        $this->subject = new Registry(
            $this->adjustmentFactoryMock
        );
    }

    /**
     * Simple way
     */
    public function testGet(): void
    {
        $hash = 'hgkg5993dad75kl';
        $rateMock = $this->createMock(Method::class);
        $adjustmentMock = $this->createMock(AdjustmentData::class);

        $this->adjustmentFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($adjustmentMock);

        $this->assertEquals($adjustmentMock, $this->subject->get($rateMock, $hash));
    }

    /**
     * Test if storage not empty and contains already calculated adjustment
     */
    public function testGetIfAlreadySet(): void
    {
        $hash = 'hgkg5993dad75kl';
        $carrier = 'flatrate';
        $method = 'fixed';
        $cacheKey = $carrier . Registry::KEY_SEPARATOR . $method;
        $adjustmentMock = $this->createMock(AdjustmentData::class);

        $class = new \ReflectionClass($this->subject);
        $property = $class->getProperty('storage');
        $property->setAccessible(true);
        $property->setValue($this->subject, [$cacheKey => [$hash => $adjustmentMock]]);
        $rateMock = $this->createMock(Method::class);

        $rateMock->expects($this->exactly(2))
            ->method('__call')
            ->withConsecutive(
                ['getCarrier', []],
                ['getMethod', []]
            )->willReturnOnConsecutiveCalls(
                $carrier,
                $method
            );

        $this->assertEquals($adjustmentMock, $this->subject->get($rateMock, $hash));
    }

    /**
     * @param bool $isExist
     * @dataProvider getListForRateProvider
     */
    public function testGetListForRate(bool $isExist): void
    {
        $hash = 'hgkg5993dad75kl';
        $carrier = 'flatrate';
        $method = 'fixed';
        $cacheKey = $carrier . Registry::KEY_SEPARATOR . $method;
        $adjustmentMock = $this->createMock(AdjustmentData::class);
        $rateMock = $this->createMock(Method::class);
        $result = $isExist ? [$hash => $adjustmentMock] : [];

        if ($isExist) {
            $class = new \ReflectionClass($this->subject);
            $property = $class->getProperty('storage');
            $property->setAccessible(true);
            $property->setValue($this->subject, [$cacheKey => [$hash => $adjustmentMock]]);
        }

        $rateMock->expects($this->exactly(2))
            ->method('__call')
            ->withConsecutive(
                ['getCarrier', []],
                ['getMethod', []]
            )->willReturnOnConsecutiveCalls(
                $carrier,
                $method
            );

        $this->assertEquals($result, $this->subject->getListForRate($rateMock));
    }

    /**
     * @return array
     */
    public function getListForRateProvider(): array
    {
        return [
            [true],
            [false]
        ];
    }
}
