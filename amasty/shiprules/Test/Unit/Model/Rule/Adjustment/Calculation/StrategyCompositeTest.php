<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Test\Unit\Model\Rule\Adjustment\Calculation;

use Amasty\CommonRules\Model\OptionProvider\Provider\CalculationOptionProvider;
use Amasty\Shiprules\Api\Data\RuleInterface;
use Amasty\Shiprules\Model\Rule\Adjustment\Calculation\StrategyComposite;
use Amasty\Shiprules\Model\Rule\Adjustment\Calculation\StrategyInterface;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use PHPUnit\Framework\TestCase;

class StrategyCompositeTest extends TestCase
{
    /**
     * @var StrategyComposite
     */
    private $subject;

    public function setUp(): void
    {
        $this->subject = new StrategyComposite();
    }

    /**
     * Positive way
     */
    public function testGetAdjustmentValue(): void
    {
        $ruleMock = $this->createConfiguredMock(
            RuleInterface::class,
            ['getCalc' => CalculationOptionProvider::CALC_ADD]
        );
        $methodMock = $this->createMock(Method::class);
        $strategyMock = $this->createMock(StrategyInterface::class);
        $amount = 5.0;
        $dummyStrategyIndex = CalculationOptionProvider::CALC_ADD;

        $class = new \ReflectionClass($this->subject);
        $property = $class->getProperty('strategies');
        $property->setAccessible(true);
        $property->setValue($this->subject, [$dummyStrategyIndex => $strategyMock]);

        $strategyMock->expects($this->once())
            ->method('getAdjustmentValue')
            ->with($methodMock, $ruleMock, $amount)
            ->willReturn($amount);

        $this->assertEquals($amount, $this->subject->getAdjustmentValue($methodMock, $ruleMock, $amount));
    }

    /**
     * Test if strategy doesn't exist or not present
     */
    public function testGetAdjustmentValueWithException(): void
    {
        $ruleMock = $this->createConfiguredMock(
            RuleInterface::class,
            ['getCalc' => CalculationOptionProvider::CALC_ADD]
        );
        $methodMock = $this->createMock(Method::class);
        $amount = 5.0;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid strategy class provided. Expected implementation of ' . StrategyInterface::class
        );

        $this->subject->getAdjustmentValue($methodMock, $ruleMock, $amount);
    }
}
