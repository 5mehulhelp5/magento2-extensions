<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Test\Unit\Model\Email;

use Amasty\AdvancedReview\Helper\Config;
use Amasty\AdvancedReview\Model\Email\Coupon;
use Amasty\AdvancedReview\Model\Email\CouponConditionsProvider;
use Amasty\AdvancedReview\Model\Email\CouponDataProvider;
use Amasty\AdvancedReview\Test\Unit\Traits;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;
use Magento\Store\Model\Website;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class CouponTest
 *
 * @SuppressWarnings(PHPMD.CouplingBetwgroupCollectionnObjects)
 * phpcs:ignoreFile
 */
class CouponTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var MockObject|Config
     */
    private $configHelper;

    /**
     * @var MockObject|CollectionFactory
     */
    private $groupCollection;

    /**
     * @var MockObject|Coupon
     */
    private $coupon;

    /**
     * @var MockObject|CouponConditionsProvider
     */
    private $couponConditionsProvider;

    /**
     * @var MockObject|CouponDataProvider
     */
    private $couponDataProvider;

    protected function setUp(): void
    {
        $this->configHelper = $this->createMock(Config::class);
        $this->groupCollection = $this->createMock(CollectionFactory::class);
        $this->coupon = $this->createPartialMock(Coupon::class, ['generateCoupon', 'getDaysMessage']);
        $this->couponConditionsProvider = $this->createPartialMock(CouponConditionsProvider::class, ['generateConditions']);
        $this->couponDataProvider = $this->createPartialMock(CouponDataProvider::class, ['getCustomerGroupIds']);

        $this->setProperty($this->coupon, 'configHelper' , $this->configHelper);
        $this->setProperty($this->coupon, 'couponConditionsProvider' , $this->couponConditionsProvider);
        $this->setProperty($this->coupon, 'couponDataProvider' , $this->couponDataProvider);
    }

    /**
     * @covers \Amasty\AdvancedReview\Model\Email\Coupon::getCouponMessage
     *
     * @dataProvider getCouponMessageDataProvider
     */
    public function testGetCouponMessage(
        $isAllowCoupons,
        $isNeedReview,
        $resultTest,
        $couponCode = '',
        $day = '',
        $dayMessage = ''
    ) {
        $website = $this->createMock(Website::class);

        $this->configHelper->expects($this->any())->method('isAllowCoupons')->willReturn($isAllowCoupons);
        $this->configHelper->expects($this->any())->method('isNeedReview')->willReturn($isNeedReview);
        $this->configHelper->expects($this->any())->method('getModuleConfig')->willReturn($day);
        $this->coupon->expects($this->any())->method('generateCoupon')->willReturn($couponCode);
        $this->coupon->expects($this->any())->method('getDaysMessage')->willReturn($dayMessage);

        $result = $this->invokeMethod($this->coupon, 'getCouponMessage', [$website]);
        if (is_object($result)) {
            $result = $result->render();
        }
        $this->assertEquals($resultTest, $result);
    }

    /**
     * Data provider for getCouponMessage test
     * @return array
     */
    public function getCouponMessageDataProvider()
    {
        return [
            [
                true,
                true,
                'It will take only a few minutes, just click the \'Leave a review\' button below. And please kindly '
                . 'keep in mind, that you will receive a discount coupon after your review is approved.'
            ],
            [
                false,
                true,
                'It will take only a few minutes, just click the \'Leave a review\' button below.'
            ],
            [
                true,
                false,
                '<p class="amcomment">It will take only a few minutes, just click the \'Leave a review\' button below.</p><p class="am-coupon-container">To make the '
                . 'process more pleasant we are happy to grant you a discount coupon code, which can already be used '
                . 'for your next purchase. Here it is: <span class="coupon">CouponCode</span> (please kindly keep in mind that it will expire in '
                . '5 days).</p>',
                'CouponCode',
                '5',
                'please kindly keep in mind that it will expire in 5 days'
            ]
        ];
    }
}
