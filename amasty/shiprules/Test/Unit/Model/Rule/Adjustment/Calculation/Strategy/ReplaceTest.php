<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Test\Unit\Model\Rule\Adjustment\Calculation\Strategy;

use Amasty\Shiprules\Api\Data\RuleInterface;
use Amasty\Shiprules\Model\Rule\Adjustment\Calculation\Strategy\Replace;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use PHPUnit\Framework\TestCase;

class ReplaceTest extends TestCase
{
    /**
     * @var Replace
     */
    private $subject;

    public function setUp(): void
    {
        $this->subject = new Replace();
    }

    public function testGetAdjustmentValue(): void
    {
        $amount = 5.0;
        $methodPrice = 2.0;
        $ruleMock = $this->createMock(RuleInterface::class);
        $methodMock = $this->createConfiguredMock(
            Method::class,
            ['__call' => $methodPrice]
        );

        $this->assertEquals(
            $amount - $methodPrice,
            $this->subject->getAdjustmentValue($methodMock, $ruleMock, $amount)
        );
    }
}
