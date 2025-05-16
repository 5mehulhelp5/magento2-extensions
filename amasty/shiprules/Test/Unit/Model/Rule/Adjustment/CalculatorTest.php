<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Test\Unit\Model\Rule\Adjustment;

use Amasty\Shiprules\Model\Rule;
use Amasty\Shiprules\Model\Rule\Adjustment\Calculation\StrategyComposite;
use Amasty\Shiprules\Model\Rule\Adjustment\Calculator;
use Amasty\Shiprules\Model\Rule\Adjustment\Total;
use Amasty\Shiprules\Model\Rule\Adjustment\Total\Registry as TotalRegistry;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    /**
     * @var TotalRegistry|MockObject
     */
    private $totalRegistryMock;

    /**
     * @var StrategyComposite|MockObject
     */
    private $calculationStrategyMock;

    /**
     * @var Calculator
     */
    private $subject;

    public function setUp(): void
    {
        $this->totalRegistryMock = $this->createMock(TotalRegistry::class);
        $this->calculationStrategyMock = $this->createMock(StrategyComposite::class);
        $this->subject = new Calculator(
            $this->totalRegistryMock,
            $this->calculationStrategyMock
        );
    }

    public function testCalculateByRule(): void
    {
        $hash = 'hgkg5993dad75kl';
        $ruleRate = 2.00;
        $ruleMax = 8.00;
        $ruleMin = 1.00;
        $rateMock = $this->createMock(Method::class);
        $totalMock = $this->createMock(Total::class);
        $ruleMock = $this->createConfiguredMock(
            Rule::class,
            [
                'getRateBase' => $ruleRate,
                'getRateMin' => $ruleMin,
                'getRateMax' => $ruleMax
            ]
        );

        $totalMock->expects($this->atMost(1))
            ->method('getNotFreeQty')
            ->willReturn(1.0);

        $this->totalRegistryMock->expects($this->atMost(1))
            ->method('getByHash')
            ->with($hash)
            ->willReturn($totalMock);

        $this->calculationStrategyMock->expects($this->atMost(1))
            ->method('getAdjustmentValue')
            ->with($rateMock, $ruleMock, $ruleRate)
            ->willReturn($ruleRate);

        $this->assertEquals($ruleRate, $this->subject->calculateByRule($ruleMock, $rateMock, $hash));
    }

    public function testCalculateByRuleIfAlreadySet(): void
    {
        $hash = 'hgkg5993dad75kl';
        $carrier = 'flatrate';
        $method = 'fixed';
        $ruleRate = 2.00;
        $cacheKey = $carrier . $method;
        $class = new \ReflectionClass($this->subject);
        $property = $class->getProperty('ratePrice');
        $property->setAccessible(true);
        $property->setValue($this->subject, [$cacheKey => $ruleRate]);
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

        $this->assertEquals(
            $ruleRate,
            $this->subject->calculateByRule(
                $this->createMock(Rule::class),
                $rateMock,
                $hash
            )
        );
    }

    public function testCalculateByRuleIfTotalNotExist(): void
    {
        $hash = 'hgkg5993dad75kl';
        $rateMock = $this->createMock(Method::class);
        $totalMock = $this->createMock(Total::class);
        $ruleMock = $this->createMock(Rule::class);

        $this->totalRegistryMock->expects($this->atMost(1))
            ->method('getByHash')
            ->with($hash)
            ->willReturn(null);

        $this->assertEquals(0, $this->subject->calculateByRule($ruleMock, $rateMock, $hash));
    }
}
