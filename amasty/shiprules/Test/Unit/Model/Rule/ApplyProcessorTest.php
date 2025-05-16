<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Test\Unit\Model\Rule;

use Amasty\Shiprules\Api\ShippingRuleApplierInterface;
use Amasty\Shiprules\Model\Rule;
use Amasty\Shiprules\Model\Rule\ApplyProcessor;
use Amasty\Shiprules\Model\Rule\Quote\Address\RateRequest\Modifier as RequestModifier;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Item;
use Magento\Shipping\Model\Rate\CarrierResult;
use Magento\Shipping\Model\Shipping;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ApplyProcessorTest extends TestCase
{
    /**
     * @var ShippingRuleApplierInterface|MockObject
     */
    private $applierMock;

    /**
     * @var RequestModifier|MockObject
     */
    private $requestModifierMock;

    /**
     * @var ApplyProcessor
     */
    private $subject;

    public function setUp(): void
    {
        $this->applierMock = $this->createMock(ShippingRuleApplierInterface::class);
        $this->requestModifierMock = $this->createMock(RequestModifier::class);

        $this->subject = new ApplyProcessor($this->applierMock, $this->requestModifierMock);
    }

    /**
     * @param bool $canApply
     * @param array $rules
     * @param MockObject|null $newRateRequestMock
     * @dataProvider processPartialCartProvider
     */
    public function testProcessPartialCart(bool $canApply, array $rules, ?MockObject $newRateRequestMock): void
    {
        $shippingMock = $this->createMock(Shipping::class);
        $rateRequestMock = $this->createMock(RateRequest::class);
        $resultMock = $this->createMock(CarrierResult::class);
        $rateMock = $this->createMock(Method::class);
        $rates = [$rateMock];

        $shippingMock->expects($this->once())
            ->method('getResult')
            ->willReturn($resultMock);

        $resultMock->expects($this->atLeastOnce())
            ->method('getAllRates')
            ->willReturn([$rateMock]);

        $this->applierMock->expects($this->once())
            ->method('canApplyAnyRule')
            ->willReturn($canApply);

        if ($canApply) {
            $this->applierMock->expects($this->exactly(count($rates)))
                ->method('getRulesForCarrier')
                ->willReturn($rules);

            $this->applierMock->expects($this->exactly(count($rules)))
                ->method('getModifiedRequest')
                ->with($rateMock, $rateRequestMock, current($rules))
                ->willReturn($newRateRequestMock);

            $this->applierMock->expects($this->exactly(count($rates)))
                ->method('applyAdjustment');
        }

        $this->subject->process($shippingMock, $rateRequestMock);
    }

    /**
     * @param bool $hasNotCoveredItems
     * @dataProvider processFullCartProvider
     */
    public function testProcessFullCart(bool $hasNotCoveredItems): void
    {
        $ruleMock = $this->createMock(Rule::class);
        $rules = [$ruleMock];
        $shippingMock = $this->createMock(Shipping::class);
        $itemMock = $this->createConfiguredMock(Item::class, ['getId' => 1]);
        $rateRequestMock = $this->createConfiguredMock(
            RateRequest::class,
            ['__call' => $hasNotCoveredItems ? [$itemMock] : []]
        );
        $resultMock = $this->createMock(CarrierResult::class);
        $rateMock = $this->createMock(Method::class);
        $rates = [$rateMock];

        $shippingMock->expects($this->atLeastOnce())
            ->method('getResult')
            ->willReturn($resultMock);

        $resultMock->expects($this->atLeastOnce())
            ->method('getAllRates')
            ->willReturn([$rateMock]);

        $this->applierMock->expects($this->once())
            ->method('canApplyAnyRule')
            ->willReturn(true);

        $this->applierMock->expects($this->exactly(count($rates)))
            ->method('getRulesForCarrier')
            ->with($rateMock)
            ->willReturn($rules);

        $this->applierMock->expects($this->exactly(count($rates)))
            ->method('getRulesForCarrier')
            ->willReturn($rules);

        $this->applierMock->expects($this->exactly(count($rules)))
            ->method('getModifiedRequest')
            ->willReturn(null);

        if ($hasNotCoveredItems) {
            $this->requestModifierMock->expects($this->once())
                ->method('modify')
                ->with($rateRequestMock, null)
                ->willReturn($rateRequestMock);

            $this->applierMock->expects($this->once())
                ->method('calculateAdjustments')
                ->with($rates)
                ->willReturn($rateRequestMock);
        }

        $this->applierMock->expects($this->exactly(count($rates)))
            ->method('applyAdjustment');

        $this->subject->process($shippingMock, $rateRequestMock);
    }

    /**
     * @return array[]
     */
    public function processPartialCartProvider(): array
    {
        $ruleMock = $this->createMock(Rule::class);

        return [
            [true, [$ruleMock], null],
            [false, [$ruleMock], $this->createMock(RateRequest::class)]
        ];
    }

    /**
     * @return array
     */
    public function processFullCartProvider(): array
    {
        return [
            [true],
            [false]
        ];
    }
}
