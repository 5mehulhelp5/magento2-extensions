<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Test\Unit\Model\Rule;

use Amasty\Shiprules\Api\Data\RuleInterface;
use Amasty\Shiprules\Model\Rule;
use Amasty\Shiprules\Model\Rule\Adjustment\Calculator;
use Amasty\Shiprules\Model\Rule\Adjustment\Registry;
use Amasty\Shiprules\Model\Rule\AdjustmentData;
use Amasty\Shiprules\Model\Rule\Applier;
use Amasty\Shiprules\Model\Rule\Items\Provider as ItemsProvider;
use Amasty\Shiprules\Model\Rule\Provider as RulesProvider;
use Amasty\Shiprules\Model\Rule\Quote\Address\HashProvider;
use Amasty\Shiprules\Model\Rule\Quote\Address\RateRequest\Provider as RateRequestProvider;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Error;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Item;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ApplierTest extends TestCase
{
    public const HASH = '3jj59fadk856';

    /**
     * @var Registry|MockObject
     */
    private $adjustmentRegistryMock;

    /**
     * @var Calculator|MockObject
     */
    private $adjustmentCalculatorMock;

    /**
     * @var HashProvider|MockObject
     */
    private $hashProviderMock;

    /**
     * @var ItemsProvider|MockObject
     */
    private $itemsProviderMock;

    /**
     * @var RulesProvider|MockObject
     */
    private $rulesProviderMock;

    /**
     * @var RateRequestProvider|MockObject
     */
    private $rateRequestProviderMock;

    /**
     * @var Applier
     */
    private $subject;

    public function setUp(): void
    {
        $this->adjustmentRegistryMock = $this->createMock(Registry::class);
        $this->adjustmentCalculatorMock = $this->createMock(Calculator::class);
        $this->itemsProviderMock = $this->createMock(ItemsProvider::class);
        $this->rulesProviderMock = $this->createMock(RulesProvider::class);
        $this->rateRequestProviderMock = $this->createMock(RateRequestProvider::class);
        $this->hashProviderMock = $this->createConfiguredMock(HashProvider::class, ['getHash' => self::HASH]);

        $this->subject = new Applier(
            $this->adjustmentRegistryMock,
            $this->adjustmentCalculatorMock,
            $this->itemsProviderMock,
            $this->rulesProviderMock,
            $this->rateRequestProviderMock,
            $this->hashProviderMock,
        );
    }

    public function testApplyAdjustment(): void
    {
        $methodMock = $this->createMock(Method::class);
        $adjustmentMock = $this->createMock(AdjustmentData::class);
        $adjustmentValue = 5.0;
        $minMaxRange = [
            AdjustmentData::MAX => 5,
            AdjustmentData::MIN => 1
        ];

        $this->adjustmentRegistryMock->expects($this->once())
            ->method('getListForRate')
            ->with($methodMock)
            ->willReturn([$adjustmentMock]);
        $adjustmentMock->expects($this->once())
            ->method('getValue')
            ->willReturn($adjustmentValue);
        $adjustmentMock->expects($this->once())
            ->method('getRateTotalRange')
            ->willReturn($minMaxRange);

        $this->subject->applyAdjustment($methodMock);
    }

    /**
     * @param array $items
     * @param bool|RateRequest|MockObject $result
     * @dataProvider getModifiedRequestProvider
     */
    public function testGetModifiedRequest(array $items, $result): void
    {
        $ruleMock = $this->createConfiguredMock(Rule::class, ['match' => true]);
        $requestMock = $this->createMock(RateRequest::class);
        $methodMock = $this->createMock(Method::class);

        $class = new \ReflectionClass($this->subject);
        $property = $class->getProperty('allItemsId');
        $property->setAccessible(true);
        $property->setValue($this->subject, [0, 1]);

        $this->itemsProviderMock->expects($this->once())
            ->method('getValidItems')
            ->with($ruleMock)
            ->willReturn($items);
        $this->rateRequestProviderMock->expects($this->exactly($result ? 1: 0))
            ->method('getForItems')
            ->with($requestMock, $methodMock, $items)
            ->willReturn($result);

        $this->assertEquals($result, $this->subject->getModifiedRequest($methodMock, $requestMock, $ruleMock));
    }

    /**
     * @param array $methods
     * @param string $rateCode
     * @dataProvider calculateAdjustmentsProvider
     */
    public function testCalculateAdjustments(array $methods, string $rateCode): void
    {
        $itemMock = $this->createMock(Item::class);
        $ruleMock = $this->createMock(Rule::class);
        $adjustmentMock = $this->createMock(AdjustmentData::class);

        $class = new \ReflectionClass($this->subject);
        $property = $class->getProperty('allItemsId');
        $property->setAccessible(true);
        $property->setValue($this->subject, [0]);
        $property = $class->getProperty('rulesByCarrier');
        $property->setAccessible(true);
        $property->setValue($this->subject, [$rateCode => [$ruleMock]]);

        $this->adjustmentRegistryMock->expects($this->atLeastOnce())
            ->method('get')
            ->willReturn($adjustmentMock);
        $this->itemsProviderMock->expects($this->once())
            ->method('getValidItems')
            ->with($ruleMock)
            ->willReturn([$itemMock]);
        $this->adjustmentCalculatorMock->expects($this->atLeastOnce())
            ->method('calculateByRule');

        $this->subject->calculateAdjustments($methods);
    }

    public function testCalculateRateAdjustment(): void
    {
        $itemMock = $this->createMock(Item::class);
        $ruleMock = $this->createMock(Rule::class);
        $adjustmentMock = $this->createMock(AdjustmentData::class);
        $requestMock = $this->createConfiguredMock(RateRequest::class, ['__call' => [$itemMock]]);
        $carrier = 'flatrate';
        $method = 'fixed';
        $methodMock = $this->createMock(Method::class);
        $rateCode = $carrier . '_' . $method;

        $methodMock->expects($this->exactly(2))
            ->method('__call')
            ->withConsecutive(
                ['getCarrier', []],
                ['getMethod', []]
            )->willReturnOnConsecutiveCalls(
                $carrier,
                $method
            );

        $class = new \ReflectionClass($this->subject);
        $property = $class->getProperty('rulesByCarrier');
        $property->setAccessible(true);
        $property->setValue($this->subject, [$rateCode => [$ruleMock]]);

        $this->adjustmentRegistryMock->expects($this->atLeastOnce())
            ->method('get')
            ->willReturn($adjustmentMock);
        $this->itemsProviderMock->expects($this->once())
            ->method('getAllItemIds')
            ->willReturn([0]);
        $this->itemsProviderMock->expects($this->once())
            ->method('getValidItems')
            ->with($ruleMock)
            ->willReturn([$itemMock]);
        $this->adjustmentCalculatorMock->expects($this->atLeastOnce())
            ->method('calculateByRule');

        $this->subject->calculateRateAdjustment($methodMock, $requestMock);
    }

    /**
     * @param array $methods
     * @param array $rules
     * @param bool $result
     * @dataProvider canApplyAnyRuleProvider
     */
    public function testCanApplyAnyRule(array $methods, array $rules, bool $result): void
    {
        $itemMock = $this->createMock(Item::class);
        $requestMock = $this->createConfiguredMock(RateRequest::class, ['__call' => [$itemMock]]);

        $this->rulesProviderMock->expects($this->once())
            ->method('reset');
        $this->adjustmentRegistryMock->expects($this->once())
            ->method('reset');
        $this->rateRequestProviderMock->expects($this->once())
            ->method('reset');
        $this->rulesProviderMock->expects($this->once())
            ->method('getValidRules')
            ->with($requestMock)
            ->willReturn($rules);
        $this->itemsProviderMock->expects($this->once())
            ->method('getAllItemIds')
            ->willReturn([0]);

        $this->assertEquals($result, $this->subject->canApplyAnyRule($requestMock, $methods));
    }

    /**
     * @return array[]
     */
    public function getModifiedRequestProvider(): array
    {
        return [
            [[$this->createMock(Item::class)], $this->createMock(RateRequest::class)],
            [[], false]
        ];
    }

    /**
     * @return \array[][]
     */
    public function calculateAdjustmentsProvider(): array
    {
        $carrier = 'flatrate';
        $method = 'fixed';
        $methodMock1 = $this->createMock(Method::class);
        $methodMock2 = $this->createMock(Method::class);
        $rateCode = $carrier . '_' . $method;

        $methodMock1->expects($this->exactly(2))
            ->method('__call')
            ->withConsecutive(
                ['getCarrier', []],
                ['getMethod', []]
            )->willReturnOnConsecutiveCalls(
                $carrier,
                $method
            );
        $methodMock2->expects($this->exactly(2))
            ->method('__call')
            ->withConsecutive(
                ['getCarrier', []],
                ['getMethod', []]
            )->willReturnOnConsecutiveCalls(
                $carrier,
                $method
            );

        return [
            [[$methodMock1, $this->createMock(Error::class)], $rateCode],
            [[$methodMock2], $rateCode]
        ];
    }

    /**
     * @return array[]
     */
    public function canApplyAnyRuleProvider(): array
    {
        $carrier = 'flatrate';
        $method = 'fixed';
        $validRuleMock = $this->createConfiguredMock(Rule::class, ['match' => true]);
        $invalidRuleMock = $this->createConfiguredMock(Rule::class, ['match' => false]);
        $methodMock1 = $this->createMock(Method::class);
        $methodMock2 = $this->createMock(Method::class);

        $methodMock1->expects($this->exactly(2))
            ->method('__call')
            ->withConsecutive(
                ['getCarrier', []],
                ['getMethod', []]
            )->willReturnOnConsecutiveCalls(
                $carrier,
                $method
            );

        return [
            [[$methodMock1, $this->createMock(Error::class)], [$validRuleMock], true],
            [[$methodMock2], [$invalidRuleMock], false]
        ];
    }
}
