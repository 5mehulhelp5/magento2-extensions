<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\Email;

use Amasty\AdvancedReview\Helper\Config;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as GroupCollectionFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\SalesRule\Model\Rule;
use Magento\Store\Api\Data\WebsiteInterface;

class CouponDataProvider
{

    /**
     * @var int
     */
    public const STATUS_ACTIVE = 1;

    /**
     * @var Config
     */
    private $configHelper;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var GroupCollectionFactory
     */
    private $groupCollectionFactory;

    /**
     * @var RuleCollectionFactory
     */
    private $ruleCollectionFactory;

    /**
     * @var string
     */
    public static $baseRuleName = 'Amasty Review Reminder Coupons';

    /**
     * CouponDataProvider constructor.
     *
     * @param Config                 $configHelper
     * @param DateTime               $date
     * @param GroupCollectionFactory $groupCollectionFactory
     * @param RuleCollectionFactory  $ruleCollectionFactory
     */
    public function __construct(
        Config $configHelper,
        DateTime $date,
        GroupCollectionFactory $groupCollectionFactory,
        RuleCollectionFactory $ruleCollectionFactory
    ) {
        $this->configHelper           = $configHelper;
        $this->date                   = $date;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->ruleCollectionFactory  = $ruleCollectionFactory;
    }

    /**
     * @param WebsiteInterface $website
     * @return array
     */
    public function generateCouponData(WebsiteInterface $website): array
    {
        $collection = $this->ruleCollectionFactory->create()
            ->addFieldToFilter(
                'name',
                ['like' => sprintf('%s - %s', self::$baseRuleName, $website->getName()) . '%']
            );

        $days = (int) $this->configHelper->getModuleConfig('coupons/coupon_days');
        $ruleName = sprintf(
            '%s - %s - %d',
            self::$baseRuleName,
            $website->getName(),
            $collection->getSize() + 1
        );

        return [
            'name'                  => $ruleName,
            'is_active'             => self::STATUS_ACTIVE,
            'coupon_type'           => Rule::COUPON_TYPE_SPECIFIC,
            'use_auto_generation'   => 1,
            'stop_rules_processing' => 0,
            'uses_per_coupon'       =>
                (int) $this->configHelper->getModuleConfig('coupons/coupon_uses', $website->getDefaultStore()),
            'uses_per_customer'     => (int) $this->configHelper->getModuleConfig(
                'coupons/uses_per_customer',
                $website->getDefaultStore()
            ),
            'from_date'             => $this->date->date('Y-m-d'),
            'to_date'               => $this->date->date('Y-m-d', strtotime("+$days days")),
            'simple_action'         =>
                $this->configHelper->getModuleConfig('coupons/discount_type', $website->getDefaultStore()),
            'discount_amount'       =>
                $this->configHelper->getModuleConfig('coupons/discount_amount', $website->getDefaultStore()),
            'website_ids'           => [$website->getId()],
            'customer_group_ids'    => $this->getCustomerGroupIds()
        ];
    }

    /**
     * @return array
     */
    private function getCustomerGroupIds(): array
    {
        if (empty($customerGroupIds = $this->configHelper->getCustomerGroups())) {
            $customerGroups = $this->groupCollectionFactory->create();
            foreach ($customerGroups as $group) {
                $customerGroupIds[] = $group->getId();
            }
        }

        return $customerGroupIds;
    }
}
