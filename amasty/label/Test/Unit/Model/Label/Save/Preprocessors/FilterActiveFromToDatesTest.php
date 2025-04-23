<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Test\Unit\Model\Label\Save\Preprocessors;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Model\Label\Save\Preprocessors\FilterActiveFromToDates;
use Amasty\Label\Model\ScopeDateValidator;
use Magento\Framework\Stdlib\DateTime\Filter\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use PHPUnit\Framework\TestCase;

/**
 * @see \Amasty\Label\Model\Label\Save\Preprocessors\FilterActiveFromToDates
 */
class FilterActiveFromToDatesTest extends TestCase
{
    /**
     * @covers \Amasty\Label\Model\Label\Save\Preprocessors\FilterActiveFromToDates::process
     * @dataProvider getProcessorDataProvider
     *
     * @param array $data
     * @param array $expected
     */
    public function testProcess(array $data, array $expected): void
    {
        $dateFilter = $this->createMock(DateTime::class);
        $timezone = $this->createMock(TimezoneInterface::class);
        $dateObject = $this->createMock(\DateTime::class);
        $scopeDateValidator = $this->createMock(ScopeDateValidator::class);

        $dateFilter
            ->expects($this->any())
            ->method('filter')
            ->willReturn('Fri, 12 Mar 2021 22:39:13 GMT');

        $timezone->expects($this->any())->method('scopeDate')->willReturn($dateObject);

        $processor = new FilterActiveFromToDates($dateFilter, $scopeDateValidator, $timezone);

        $this->assertEquals(
            $expected,
            $processor->process($data)
        );
    }

    /**
     * Data provider for process test
     * @return array
     */
    public function getProcessorDataProvider(): array
    {
        return [
            [
                [
                    LabelInterface::STATUS => true,
                    LabelInterface::ACTIVE_FROM => 'Fri, 12 Mar 2021 22:39:13 GMT',
                    LabelInterface::ACTIVE_TO => 'Fri, 12 Mar 2021 22:39:13 GMT',
                ],
                [
                    LabelInterface::STATUS => false,
                    LabelInterface::ACTIVE_FROM => 'Fri, 12 Mar 2021 22:39:13 GMT',
                    LabelInterface::ACTIVE_TO => 'Fri, 12 Mar 2021 22:39:13 GMT',
                ]
            ]
        ];
    }
}
