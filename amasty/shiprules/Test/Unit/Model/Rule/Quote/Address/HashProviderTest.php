<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Test\Unit\Model\Rule\Quote\Address;

use Amasty\CommonRules\Model\Rule\Condition\Address;
use Amasty\Shiprules\Model\Rule\Items\Provider;
use Amasty\Shiprules\Model\Rule\Quote\Address\HashProvider;
use Magento\Quote\Model\Quote\Address\RateRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HashProviderTest extends TestCase
{
    /**
     * @var Address|MockObject
     */
    private $addressConditionMock;

    /**
     * @var Provider|MockObject
     */
    private $itemsProviderMock;

    /**
     * @var HashProvider
     */
    private $subject;

    public function setUp(): void
    {
        $this->addressConditionMock = $this->createMock(Address::class);
        $this->itemsProviderMock = $this->createMock(Provider::class);
        $this->subject = new HashProvider($this->addressConditionMock, $this->itemsProviderMock);
    }

    public function testGetHash(): void
    {
        $rateRequestMock = $this->createMock(RateRequest::class);
        $addressAttributes = [
            'dest_country_id' => 'dest_country_id',
            'dest_region_id' => 'dest_region_id',
            'dest_city' => 'dest_city',
            'dest_postcode' => 'dest_postcode',
        ];
        $strSource = implode('', $addressAttributes);
        $hash = hash('md5', $strSource);

        $this->addressConditionMock->expects($this->once())
            ->method('__call')
            ->with('getAttributeOption', [])
            ->willReturn([]);

        $rateRequestMock->expects($this->once())
            ->method('__call')
            ->with('getAllItems')
            ->willReturn([]);

        $this->assertEquals($hash, $this->subject->getHash($rateRequestMock));
    }
}
