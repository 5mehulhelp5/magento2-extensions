<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Test\Unit\Model\Rule;

use Amasty\CommonRules\Model\Modifiers\Address;
use Amasty\CommonRules\Model\Validator\SalesRule;
use Amasty\Shiprules\Model\Rule;
use Amasty\Shiprules\Model\Rule\Adjustment\Total;
use Amasty\Shiprules\Model\Rule\Adjustment\Total\Validator as TotalValidator;
use Amasty\Shiprules\Model\Rule\Validator;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Item;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    /**
     * @var Address|MockObject
     */
    private $addressModifierMock;

    /**
     * @var SalesRule|MockObject
     */
    private $salesRuleValidatorMock;

    /**
     * @var TotalValidator
     */
    private $totalValidatorMock;

    /**
     * @var Validator
     */
    private $subject;

    public function setUp(): void
    {
        $this->addressModifierMock = $this->createMock(Address::class);
        $this->salesRuleValidatorMock = $this->createMock(SalesRule::class);
        $this->totalValidatorMock = $this->createMock(TotalValidator::class);

        $this->subject = new Validator(
            $this->addressModifierMock,
            $this->salesRuleValidatorMock,
            $this->totalValidatorMock
        );
    }

    /**
     * @param bool $isValidRule
     * @param bool $isSalesValid
     * @param bool $isTotalsValid
     * @dataProvider validateRuleProvider
     */
    public function testValidateRule(bool $isValidRule, bool $isSalesValid, bool $isTotalsValid): void
    {
        $result = $isTotalsValid && $isSalesValid && $isValidRule;
        $totalMock = $this->createMock(Total::class);
        $shippingMock = $this->createMock(Quote\Address::class);

        $itemMock = $this->createConfiguredMock(
            Item::class,
            ['getAddress' => $shippingMock]
        );
        $requestMock = $this->createConfiguredMock(
            RateRequest::class,
            ['__call' => [$itemMock]]
        );
        $ruleMock = $this->createConfiguredMock(
            Rule::class,
            ['validate' => $isValidRule]
        );

        $this->addressModifierMock->expects($this->once())
            ->method('modify')
            ->with($shippingMock, $requestMock)
            ->willReturn($shippingMock);
        $this->salesRuleValidatorMock->expects($this->any())
            ->method('validate')
            ->with($ruleMock, [$itemMock])
            ->willReturn($isSalesValid);
        $this->totalValidatorMock->expects($this->any())
            ->method('validate')
            ->with($ruleMock, $totalMock)
            ->willReturn($isTotalsValid);
        
        $this->assertEquals($result, $this->subject->validateRule($ruleMock, $requestMock, $totalMock));
    }

    public function testValidateRuleWithoutItems(): void
    {
        $totalMock = $this->createMock(Total::class);
        $ruleMock = $this->createMock(Rule::class);
        $requestMock = $this->createConfiguredMock(
            RateRequest::class,
            ['__call' => []]
        );

        $this->addressModifierMock->expects($this->never())->method('modify');
        $this->salesRuleValidatorMock->expects($this->never())->method('validate');
        $this->totalValidatorMock->expects($this->never())->method('validate');
        $ruleMock->expects($this->never())->method('validate');

        $this->assertFalse($this->subject->validateRule($ruleMock, $requestMock, $totalMock));
    }

    /**
     * @return array
     */
    public function validateRuleProvider(): array
    {
        return [
            [true, true, true],
            [false, true, true],
            [true, false, true],
            [true, true, false]
        ];
    }
}
