<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Test\Unit\Model\Rule\Quote\Address\RateRequest;

use Amasty\Shiprules\Model\Rule\Adjustment\Total;
use Amasty\Shiprules\Model\Rule\Adjustment\Total\Registry as TotalRegistry;
use Amasty\Shiprules\Model\Rule\Quote\Address\HashProvider;
use Amasty\Shiprules\Model\Rule\Quote\Address\RateRequest\Modifier;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ModifierTest extends TestCase
{
    /**
     * @var TotalRegistry|MockObject
     */
    private $totalRegistryMock;

    /**
     * @var HashProvider|MockObject
     */
    private $hashProviderMock;

    /**
     * @var Modifier
     */
    private $subject;

    public function setUp(): void
    {
        $this->totalRegistryMock = $this->createMock(TotalRegistry::class);
        $this->hashProviderMock = $this->createMock(HashProvider::class);
        $this->subject = new Modifier(
            $this->totalRegistryMock,
            $this->hashProviderMock
        );
    }

    /**
     * @param MockObject|null $methodMock
     * @dataProvider modifyProvider
     */
    public function testModify(?MockObject $methodMock = null): void
    {
        $hash = '5jj30fklsda';
        $totalMock = $this->createMock(Total::class);
        $rateRequestMock = $this->createMock(RateRequest::class);

        $this->hashProviderMock->expects($this->once())
            ->method('getHash')
            ->with($rateRequestMock)
            ->willReturn($hash);
        $this->totalRegistryMock->expects($this->once())
            ->method('getCalculatedTotal')
            ->with($rateRequestMock, $hash)
            ->willReturn($totalMock);

        $this->assertSame($rateRequestMock, $this->subject->modify($rateRequestMock, $methodMock));
    }

    public function modifyProvider(): array
    {
        return [
            [$this->createMock(Method::class)],
            [null]
        ];
    }
}
