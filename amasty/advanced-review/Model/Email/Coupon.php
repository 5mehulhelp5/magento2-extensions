<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\Email;

use Amasty\AdvancedReview\Helper\Config;
use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\SalesRule\Api\CouponRepositoryInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\SalesRule\Model\CouponFactory;
use Magento\SalesRule\Model\ResourceModel\Rule\Collection;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory;
use Magento\SalesRule\Model\Rule;
use Magento\Store\Api\Data\WebsiteInterface;
use Psr\Log\LoggerInterface;

class Coupon
{

    /**
     * @var bool
     */
    private $sendCoupon = false;

    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @var CouponDataProvider
     */
    protected $couponDataProvider;

    /**
     * @var CouponConditionsProvider
     */
    protected $couponConditionsProvider;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var CollectionFactory
     */
    private $ruleCollectionFactory;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CouponFactory
     */
    private $couponFactory;

    /**
     * @var CouponRepositoryInterface
     */
    private $couponRepository;

    /**
     * @var SalesRuleProvider
     */
    private $salesRuleProvider;

    /**
     * Coupon constructor.
     * @param Config $configHelper
     * @param DateTime $date
     * @param CollectionFactory $ruleCollectionFactory
     * @param LoggerInterface $logger
     * @param RuleRepositoryInterface $ruleRepository
     * @param CouponFactory $couponFactory
     * @param CouponRepositoryInterface $couponRepository
     * @param SalesRuleProvider $salesRuleProvider
     */
    public function __construct(
        Config $configHelper,
        DateTime $date,
        CollectionFactory $ruleCollectionFactory,
        LoggerInterface $logger,
        RuleRepositoryInterface $ruleRepository,
        CouponFactory $couponFactory,
        CouponRepositoryInterface $couponRepository,
        SalesRuleProvider $salesRuleProvider
    ) {
        $this->configHelper = $configHelper;
        $this->date = $date;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->logger = $logger;
        $this->ruleRepository = $ruleRepository;
        $this->couponFactory = $couponFactory;
        $this->couponRepository = $couponRepository;
        $this->salesRuleProvider = $salesRuleProvider;
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function removeOldCoupons(): void
    {
        /** @var Rule $rule */
        foreach ($this->getExpiredRuleCollection() as $rule) {
            try {
                $this->ruleRepository->deleteById($rule->getRuleId());
            } catch (Exception $e) {
                throw new LocalizedException(
                    __("\r\nError when deleting rule #%s : %s", $rule->getId(), $e->getMessage())
                );
            }
        }
    }

    /**
     * @return string
     */
    public function getReviewText(): string
    {
        return (string) __(
            'It will take only a few minutes, just click the \'Leave a review\' button below. And please kindly keep '
            . 'in mind, that you will receive a discount coupon after your review is approved.'
        );
    }

    /**
     * @param  int $days
     * @return string
     */
    public function getDaysMessage(int $days): string
    {
        $daysMessage = $days
            ? __('please kindly keep in mind that it will expire in %1 days', $days)
            : __('please keep in mind that it expires today');

        return (string) $daysMessage;
    }

    /**
     * @param WebsiteInterface $website
     * @return string
     */
    public function generateCoupon(WebsiteInterface $website): string
    {
        try {
            $rule = $this->salesRuleProvider->getRule($website);

            $coupon = $this->couponFactory->create();
            $store = $website->getDefaultStore();
            $coupon->setId(null)
                ->setRuleId($rule->getId())
                ->setUsageLimit((int)$this->configHelper->getModuleConfig('coupons/coupon_uses', $store))
                ->setUsagePerCustomer(
                    (int)$this->configHelper->getModuleConfig('coupons/uses_per_customer', $store)
                )
                ->setCreatedAt($this->date->date())
                ->setType(\Magento\SalesRule\Helper\Coupon::COUPON_TYPE_SPECIFIC_AUTOGENERATED)
                ->setCode($rule->getCouponCodeGenerator()->generateCode());

            $this->couponRepository->save($coupon);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $coupon->getCode() ?? '';
    }

    /**
     * @param WebsiteInterface $website
     * @return string
     */
    public function getCouponMessage(WebsiteInterface $website): string
    {
        if ($this->configHelper->isAllowCoupons()) {
            if ($this->configHelper->isNeedReview()) {
                $message = $this->getReviewText();
            } else {
                $days = (int)$this->configHelper->getModuleConfig('coupons/coupon_days');
                $couponCode = $this->generateCoupon($website);
                $message = $this->getCouponText($couponCode, $days);

                if ($couponCode) {
                    $this->sendCoupon = true;
                }
            }
        } else {
            $message = $this->getNoCouponText();
        }

        return $message;
    }

    /**
     * @return  bool
     */
    public function isCouponCodeInMessage(): bool
    {
        return $this->sendCoupon;
    }

    /**
     * @param   string $couponCode
     * @param   int $days
     * @return  string
     */
    private function getCouponText(string $couponCode, int $days): string
    {
        $daysMessage = $this->getDaysMessage($days);
        return sprintf(
            '<p class="amcomment">%s</p><p class="am-coupon-container">%s<span class="coupon">%s</span> (%s).</p>',
            __('It will take only a few minutes, just click the \'Leave a review\' button below.'),
            __(
                'To make the process more pleasant we are happy to grant you a discount coupon code,'
                . ' which can already be used for your next purchase. Here it is: '
            ),
            $couponCode,
            $daysMessage
        );
    }

    /**
     * @return string
     */
    private function getNoCouponText(): string
    {
        return (string) __('It will take only a few minutes, just click the \'Leave a review\' button below.');
    }

    /**
     * @return Collection
     */
    private function getExpiredRuleCollection(): Collection
    {
        /** @var Collection $collection */
        $collection = $this->ruleCollectionFactory->create();
        $collection->addFieldToFilter('coupon_type', ['eq' => Rule::COUPON_TYPE_SPECIFIC]);
        $collection->addFieldToFilter(
            ['name', 'name'],
            [['like' => '%@%.%'], ['like' => CouponDataProvider::$baseRuleName . ' %']]
        );
        $collection->addFieldToFilter('to_date', ['lt' => $this->date->date('Y-m-d')]);
        return $collection;
    }
}
