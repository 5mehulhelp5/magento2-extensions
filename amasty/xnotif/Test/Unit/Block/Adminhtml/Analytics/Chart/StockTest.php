<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

/**
 * @codingStandardsIgnoreFile
 */

namespace Amasty\Xnotif\Test\Unit\Block\Adminhtml\Analytics\Chart;

use Amasty\Xnotif\Block\Adminhtml\Analytics\Chart\Stock;
use Amasty\Xnotif\Model\ResourceModel\Analytics\Request\Stock\Collection as StockCollection;
use PHPUnit\Framework\MockObject\MockObject as MockObject;
use Amasty\Xnotif\Test\Unit\Traits\ObjectManagerTrait;
use Amasty\Xnotif\Test\Unit\Traits\ReflectionTrait;

/**
 * Class StockTest
 *
 * @see Stock
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StockTest extends \PHPUnit\Framework\TestCase
{
    use ReflectionTrait;
    use ObjectManagerTrait;

    /**
     * @covers Stock::getTotal
     * @dataProvider getDataForTotal
     *
     * @param array $totalRowData
     * @param string $field
     * @param string $expectedResult
     *
     * @throws \ReflectionException
     */
    public function testGetTotal($totalRowData, $field, $expectedResult)
    {
        /** @var Stock|MockObject $model */
        $model = $this->createPartialMock(Stock::class, ['getTotalRowData']);
        $model->expects($this->once())->method('getTotalRowData')->willReturn($totalRowData);

        if (!empty($totalRowData['orders'])) {
            $localeCurrency = $this->createMock(\Magento\Framework\Locale\Currency::class);
            $currency = $this->createMock(\Magento\Framework\Currency::class);
            $currency->expects($this->once())->method('toCurrency')->willReturnArgument(0);
            $localeCurrency->expects($this->once())->method('getCurrency')->willReturn($currency);

            $this->fillModelForConvert($model);
            $this->setProperty($model, 'localeCurrency', $localeCurrency, Stock::class);
        }

        $this->assertEquals($expectedResult, $model->getTotal($field));
    }

    /**
     * @covers Stock::getAnalyticsData
     * @throws \ReflectionException
     */
    public function testGetAnalyticsData()
    {
        /** @var Stock|MockObject $model */
        $model = $this->createPartialMock(Stock::class, ['getAnalyticsCollectionData']);

        $analyticDataCollection = $this->createMock(StockCollection::class);


        /** @var \Amasty\Xnotif\Model\Analytics\Request\Stock|MockObject $analyticData */
        $analyticData = $this->createPartialMock(\Amasty\Xnotif\Model\Analytics\Request\Stock::class, []);
        $analyticData->setData(
            [
                'subscribed' => '0',
                'sent' => '0',
                'orders' => null,
                'date' => '',
            ]
        );

        $dateTime = $this->createMock(\Magento\Framework\Stdlib\DateTime\DateTime::class);
        $dateTime->expects($this->once())->method('date')->willReturnCallback(
            function ($format = null) {
                return \date($format);
            }
        );

        $jsonSerializer = $this->getObjectManager()->getObject(
            \Magento\Framework\Serialize\Serializer\Json::class
        );
        $jsonEncoder = $this->getObjectManager()->getObject(
            \Magento\Framework\Json\Encoder::class,
            ['jsonSerializer' => $jsonSerializer]
        );
        $model->expects($this->once())->method('getAnalyticsCollectionData')
            ->willReturn($analyticDataCollection);
        $analyticDataCollection->expects($this->once())->method('getItems')
            ->willReturn([0 => $analyticData]);
        $this->setProperty($model, 'dateTime', $dateTime, Stock::class);
        $this->setProperty($model, 'jsonEncoder', $jsonEncoder, Stock::class);
        $this->fillModelForConvert($model);

        $this->assertJson($model->getAnalyticsData());
    }

    public function getDataForTotal()
    {
        return [
            [
                [
                    'subscribed' => '0',
                    'sent' => '0',
                    'orders' => null,
                ],
                'subscribed',
                '0'
            ],
            [
                [
                    'subscribed' => '0',
                    'sent' => '0',
                    'orders' => null,
                ],
                'orders',
                ''
            ],
            [
                [
                    'subscribed' => '0',
                    'sent' => '0',
                    'orders' => '10',
                ],
                'orders',
                '12'
            ],
        ];
    }

    /**
     * @param Stock $model
     *
     * @throws \ReflectionException
     */
    private function fillModelForConvert($model) {
        $defaultBaseCurrency = $this->createMock(\Magento\Directory\Model\Currency::class);
        $defaultBaseCurrency->expects($this->once())->method('getRate')->willReturn('1.2');

        $this->setProperty($model, 'defaultBaseCurrency', $defaultBaseCurrency, Stock::class);
    }
}
