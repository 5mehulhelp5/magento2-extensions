<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Test\Unit\Model\Rule\Quote\Address;

use Amasty\Shiprules\Model\Rule\Quote\Address\CustomerGroupProvider;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Data\Group;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Address\RateRequest;
use PHPUnit\Framework\TestCase;

class CustomerGroupProviderTest extends TestCase
{
    /**
     * @var CustomerGroupProvider
     */
    private $subject;

    public function setUp(): void
    {
        $this->subject = new CustomerGroupProvider();
    }

    /**
     * @param bool $isCustomerLoggedIn
     * @dataProvider getCustomerGroupIdProvider
     */
    public function testGetCustomerGroupId(bool $isCustomerLoggedIn): void
    {
        $groupId = $isCustomerLoggedIn ? 1 : Group::NOT_LOGGED_IN_ID;
        $customerMock = $this->createConfiguredMock(
            CustomerInterface::class,
            ['getGroupId' => 1]
        );
        $quoteMock = $this->createConfiguredMock(
            Quote::class,
            [
                '__call' => $isCustomerLoggedIn ? 1 : 0,
                'getCustomer' => $customerMock
            ]
        );
        $itemMock = $this->createConfiguredMock(
            Item::class,
            ['getQuote' => $quoteMock]
        );
        $requestMock = $this->createConfiguredMock(
            RateRequest::class,
            ['__call' => [$itemMock]]
        );

        $this->assertEquals($groupId, $this->subject->getCustomerGroupId($requestMock));
    }

    /**
     * @return array
     */
    public function getCustomerGroupIdProvider(): array
    {
        return [
            [true],
            [false]
        ];
    }
}
