<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Test\Unit\Model\Rule\Adjustment\Calculation\Strategy;

use Amasty\Shiprules\Api\Data\RuleInterface;
use Amasty\Shiprules\Model\Rule\Adjustment\Calculation\Strategy\Dummy;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use PHPUnit\Framework\TestCase;

class DummyTest extends TestCase
{
    /**
     * @var Dummy
     */
    private $subject;

    public function setUp(): void
    {
        $this->subject = new Dummy();
    }

    public function testGetAdjustmentValue(): void
    {
        $ruleMock = $this->createMock(RuleInterface::class);
        $methodMock = $this->createMock(Method::class);
        $amount = 5.0;

        $this->assertEquals($amount, $this->subject->getAdjustmentValue($methodMock, $ruleMock, $amount));
    }
}
