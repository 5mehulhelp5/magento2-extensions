<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Test\Unit\Model\Rule;

use Amasty\Shiprules\Api\Data\RuleInterface;
use Amasty\Shiprules\Api\RuleRepositoryInterface;
use Amasty\Shiprules\Model\Rule\Adjustment\Total;
use Amasty\Shiprules\Model\Rule\Adjustment\Total\Registry as TotalRegistry;
use Amasty\Shiprules\Model\Rule\Items\Provider as ItemsProvider;
use Amasty\Shiprules\Model\Rule\Provider;
use Amasty\Shiprules\Model\Rule\Quote\Address\CustomerGroupProvider;
use Amasty\Shiprules\Model\Rule\Quote\Address\HashProvider;
use Amasty\Shiprules\Model\Rule\Validator;
use Magento\Framework\App\State;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProviderTest extends TestCase
{
    public const STORE_ID = 1;
    public const AREA_FRONTEND = 'frontend';
    public const HASH = '3jj59fadk856';
    public const GROUP_ID = 1;

    /**
     * @var State|MockObject
     */
    private $appStateMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var Validator|MockObject
     */
    private $validatorMock;

    /**
     * @var HashProvider|MockObject
     */
    private $hashProviderMock;

    /**
     * @var RuleRepositoryInterface|MockObject
     */
    private $ruleRepositoryMock;

    /**
     * @var CustomerGroupProvider|MockObject
     */
    private $groupProviderMock;

    /**
     * @var ItemsProvider|MockObject
     */
    private $itemsProviderMock;

    /**
     * @var TotalRegistry|MockObject
     */
    private $totalRegistryMock;

    /**
     * @var Provider
     */
    private $subject;

    public function setUp(): void
    {
        $storeMock = $this->createConfiguredMock(StoreInterface::class, ['getId' => self::STORE_ID]);
        $this->appStateMock = $this->createConfiguredMock(
            State::class,
            ['getAreaCode' => self::AREA_FRONTEND]
        );
        $this->storeManagerMock = $this->createConfiguredMock(StoreManagerInterface::class, ['getStore' => $storeMock]);
        $this->validatorMock = $this->createMock(Validator::class);
        $this->hashProviderMock = $this->createConfiguredMock(HashProvider::class, ['getHash' => self::HASH]);
        $this->ruleRepositoryMock = $this->createMock(RuleRepositoryInterface::class);
        $this->groupProviderMock = $this->createConfiguredMock(
            CustomerGroupProvider::class,
            ['getCustomerGroupId' => self::GROUP_ID]
        );
        $this->itemsProviderMock = $this->createMock(ItemsProvider::class);
        $this->totalRegistryMock = $this->createMock(TotalRegistry::class);

        $this->subject = new Provider(
            $this->appStateMock,
            $this->storeManagerMock,
            $this->validatorMock,
            $this->hashProviderMock,
            $this->ruleRepositoryMock,
            $this->groupProviderMock,
            $this->itemsProviderMock,
            $this->totalRegistryMock
        );
    }

    /**
     * Positive way
     */
    public function testGetValidRules(): void
    {
        $ruleMock = $this->createConfiguredMock(RuleInterface::class, ['getSkipSubsequent' => true]);
        $itemMock = $this->createMock(Item::class);
        $requestMock = $this->createConfiguredMock(RateRequest::class, ['__call' => [$itemMock]]);
        $totalMock = $this->createMock(Total::class);

        $this->hashProviderMock->expects($this->any())
            ->method('getHash')
            ->with($requestMock)
            ->willReturn(self::HASH);
        $this->ruleRepositoryMock->expects($this->once())
            ->method('getRulesByParams')
            ->with(self::STORE_ID, self::GROUP_ID, false)
            ->willReturn([$ruleMock]);
        $this->itemsProviderMock->expects($this->atLeastOnce())
            ->method('getValidItems')
            ->with($ruleMock, [$itemMock])
            ->willReturn([$itemMock]);
        $this->totalRegistryMock->expects($this->atLeastOnce())
            ->method('getCalculatedTotal')
            ->with($requestMock, self::HASH)
            ->willReturn($totalMock);
        $this->validatorMock->expects($this->atLeastOnce())
            ->method('validateRule')
            ->with($ruleMock, $requestMock, $totalMock)
            ->willReturn(true);

        $this->assertEquals([$ruleMock], $this->subject->getValidRules($requestMock));
    }

    /**
     * Test if storage not empty for provided hash
     *
     * @param array $rules
     * @dataProvider getValidRulesIfPresentInStorage
     */
    public function testGetValidRulesIfPresentInStorage(array $rules): void
    {
        $itemMock = $this->createMock(Item::class);
        $requestMock = $this->createConfiguredMock(RateRequest::class, ['__call' => [$itemMock]]);

        $class = new \ReflectionClass($this->subject);
        $property = $class->getProperty('storage');
        $property->setAccessible(true);
        $property->setValue($this->subject, [self::HASH => $rules]);

        $this->hashProviderMock->expects($this->once())
            ->method('getHash')
            ->with($requestMock)
            ->willReturn(self::HASH);
        $this->ruleRepositoryMock->expects($this->never())
            ->method('getRulesByParams');
        $this->itemsProviderMock->expects($this->never())
            ->method('getValidItems');
        $this->totalRegistryMock->expects($this->never())
            ->method('getCalculatedTotal');
        $this->validatorMock->expects($this->never())
            ->method('validateRule');

        $this->assertEquals($rules, $this->subject->getValidRules($requestMock));
    }

    /**
     * Test if request doesn't have items
     */
    public function testGetValidRulesWithoutItems(): void
    {
        $requestMock = $this->createConfiguredMock(RateRequest::class, ['__call' => []]);

        $this->assertEquals([], $this->subject->getValidRules($requestMock));
    }

    /**
     * @return array[]
     */
    public function getValidRulesIfPresentInStorage(): array
    {
        return [
            [[]],
            [[$this->createMock(RuleInterface::class)]]
        ];
    }
}
