<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Test\Unit\Model\Rule\Adjustment\Total;

use Amasty\Shiprules\Model\Rule\Adjustment\Total;
use Amasty\Shiprules\Model\Rule\Adjustment\Total\Calculator;
use Amasty\Shiprules\Model\Rule\Adjustment\Total\Registry;
use Amasty\Shiprules\Model\Rule\Adjustment\TotalFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RegistryTest extends TestCase
{
    /**
     * @var TotalFactory|MockObject
     */
    private $totalFactoryMock;

    /**
     * @var Calculator|MockObject
     */
    private $calculatorMock;

    /**
     * @var Registry
     */
    private $subject;

    public function setUp(): void
    {
        $this->totalFactoryMock = $this->createMock(TotalFactory::class);
        $this->calculatorMock = $this->createMock(Calculator::class);
        $this->subject = new Registry(
            $this->totalFactoryMock,
            $this->calculatorMock
        );
    }

    /**
     * Simple way
     */
    public function testGetCalculatedTotal(): void
    {
        $hash = 'hgkg5993dad75kl';
        $rateRequestMock = $this->createMock(RateRequest::class);
        $totalMock = $this->createMock(Total::class);

        $this->totalFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($totalMock);

        $this->calculatorMock->expects($this->once())
            ->method('calculate')
            ->with($totalMock, $rateRequestMock);

        $this->assertEquals($totalMock, $this->subject->getCalculatedTotal($rateRequestMock, $hash));
    }

    /**
     * @param string $hash
     * @param array $storageData
     * @param $result
     * @throws \ReflectionException
     * @dataProvider getByHashProvider
     */
    public function testGetByHash(string $hash, array $storageData, $result): void
    {
        $class = new \ReflectionClass($this->subject);
        $property = $class->getProperty('storage');
        $property->setAccessible(true);
        $property->setValue($this->subject, $storageData);

        $this->assertEquals($result, $this->subject->getByHash($hash));
    }

    /**
     * @return array
     */
    public function getByHashProvider(): array
    {
        $hash = 'hgkg5993dad75kl';
        $totalMock = $this->createMock(Total::class);

        return [
            [$hash, [$hash => $totalMock], $totalMock],
            [$hash, [], null]
        ];
    }
}
