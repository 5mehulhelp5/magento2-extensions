<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Test\Unit\Model\Rule\Quote\Address\RateRequest;

use Amasty\Shiprules\Model\Rule\Items\Provider as ItemsProvider;
use Amasty\Shiprules\Model\Rule\Quote\Address\RateRequest\Modifier;
use Amasty\Shiprules\Model\Rule\Quote\Address\RateRequest\Provider;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Item;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProviderTest extends TestCase
{
    /**
     * @var Modifier|MockObject
     */
    private $modifierMock;

    /**
     * @var ItemsProvider|MockObject
     */
    private $itemsProviderMock;

    /**
     * @var Provider
     */
    private $subject;

    public function setUp(): void
    {
        $this->modifierMock = $this->createMock(Modifier::class);
        $this->itemsProviderMock = $this->createMock(ItemsProvider::class);
        $this->subject = new Provider($this->modifierMock, $this->itemsProviderMock);
    }

    public function testGetForItems(): void
    {
        $itemId = 11;
        $itemMock = $this->createMock(Item::class);
        $methodMock = $this->createMock(Method::class);
        $requestMock = $this->createMock(RateRequest::class);
        $newRequestMock = $this->createMock(RateRequest::class);

        $this->itemsProviderMock->expects($this->once())
            ->method('getAllItemIds')
            ->with([$itemMock])
            ->willReturn([$itemId]);
        $this->modifierMock->expects($this->once())
            ->method('modify')
            ->with($newRequestMock, $methodMock)
            ->willReturn($newRequestMock);

        $this->assertSame($newRequestMock, $this->subject->getForItems($requestMock, $methodMock, [$itemMock]));
    }

    public function testGetForItemsIfPresentInStorage(): void
    {
        $itemId = 11;
        $carrier = 'flatrate';
        $method = 'fixed';
        $cacheKey = $carrier . '_' . $method . '_' . $itemId;
        $itemMock = $this->createMock(Item::class);
        $methodMock = $this->createMock(Method::class);
        $requestMock = $this->createMock(RateRequest::class);

        $class = new \ReflectionClass($this->subject);
        $property = $class->getProperty('calculatedRequests');
        $property->setAccessible(true);
        $property->setValue($this->subject, [$cacheKey => $requestMock]);

        $methodMock->expects($this->exactly(2))
            ->method('__call')
            ->withConsecutive(
                ['getCarrier', []],
                ['getMethod', []]
            )->willReturnOnConsecutiveCalls(
                $carrier,
                $method
            );

        $this->itemsProviderMock->expects($this->once())
            ->method('getAllItemIds')
            ->with([$itemMock])
            ->willReturn([$itemId]);
        $this->modifierMock->expects($this->never())
            ->method('modify');

        $this->assertFalse($this->subject->getForItems($requestMock, $methodMock, [$itemMock]));
    }
}
