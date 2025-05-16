<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Test\Unit\Model\Rule\Adjustment\Total;

use Amasty\Shiprules\Api\Data\RuleInterface;
use Amasty\Shiprules\Model\Rule;
use Amasty\Shiprules\Model\Rule\Adjustment\Total;
use Amasty\Shiprules\Model\Rule\Adjustment\Total\Validator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    /**
     * @var Validator
     */
    private $subject;

    public function setUp(): void
    {
        $this->subject = new Validator();
    }

    /**
     * @param MockObject|RuleInterface $ruleMock
     * @param MockObject|Total $totalMock
     * @param bool $result
     * @dataProvider validateDataProvider
     */
    public function testValidate(MockObject $ruleMock, MockObject $totalMock, bool $result): void
    {
        $this->assertEquals($result, $this->subject->validate($ruleMock, $totalMock));
    }

    /**
     * @return array[]
     */
    public function validateDataProvider(): array
    {
        $minValue = 3.;
        $maxValue = 10.;
        $ruleMock = $this->createMock(Rule::class);

        $ruleMock->expects($this->any())
            ->method('getData')
            ->withConsecutive(
                ['price_from', null],
                ['price_to', null],
                ['qty_from', null],
                ['qty_to', null],
                ['weight_from', null],
                ['weight_to', null]
            )->willReturnOnConsecutiveCalls(
                $minValue,
                $maxValue,
                $minValue,
                $maxValue,
                $minValue,
                $maxValue
            );

        $totalMock = $this->createMock(Total::class);
        $totalMock->expects($this->any())
            ->method('getNotFreePrice')
            ->willReturn(5.);
        $totalMock->expects($this->any())
            ->method('getNotFreeQty')
            ->willReturn(5.);
        $totalMock->expects($this->any())
            ->method('getNotFreeWeight')
            ->willReturn(5.);
        $totalMock->expects($this->any())
            ->method('getPrice')
            ->willReturn(5.);
        $totalMock->expects($this->any())
            ->method('getQty')
            ->willReturn(5.);
        $totalMock->expects($this->any())
            ->method('getWeight')
            ->willReturn(5.);

        // Rule mock which ignore promo products
        $ruleIgnorePromoMock = clone $ruleMock;
        $ruleIgnorePromoMock->expects($this->once())
            ->method('getIgnorePromo')
            ->willReturn(1);

        // Rule mock to invalidate total by 'from' values
        $minValue = 7.;
        $ruleInvalidByFromMock = $this->createMock(Rule::class);
        $ruleInvalidByFromMock->expects($this->any())
            ->method('getData')
            ->withConsecutive(
                ['price_from', null],
                ['price_to', null],
                ['qty_from', null],
                ['qty_to', null],
                ['weight_from', null],
                ['weight_to', null]
            )->willReturnOnConsecutiveCalls(
                $minValue,
                $maxValue,
                $minValue,
                $maxValue,
                $minValue,
                $maxValue
            );

        // Rule mock to invalidate total by 'to' values
        $maxValue = 4.;
        $ruleInvalidByToMock = $this->createMock(Rule::class);
        $ruleInvalidByToMock->expects($this->any())
            ->method('getData')
            ->withConsecutive(
                ['price_from', null],
                ['price_to', null],
                ['qty_from', null],
                ['qty_to', null],
                ['weight_from', null],
                ['weight_to', null]
            )->willReturnOnConsecutiveCalls(
                $minValue,
                $maxValue,
                $minValue,
                $maxValue,
                $minValue,
                $maxValue
            );

        return [
            [$ruleMock, $totalMock, true],
            [$ruleIgnorePromoMock, $totalMock, true],
            [$ruleInvalidByFromMock, $totalMock, false],
            [$ruleInvalidByToMock, $totalMock, false]
        ];
    }
}
