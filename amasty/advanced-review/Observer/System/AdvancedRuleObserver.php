<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Observer\System;

use Amasty\AdvancedReview\Model\Email\SalesRuleProvider;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AdvancedRuleObserver implements ObserverInterface
{
    /**
     * Critical fields for generation new sales rule
     */
    public const DEPENDS_SALES_RULE_FIELD = [
        "amasty_advancedreview/coupons/discount_type",
        "amasty_advancedreview/coupons/discount_amount",
        "amasty_advancedreview/coupons/coupon_days",
        "amasty_advancedreview/coupons/coupon_uses",
        "amasty_advancedreview/coupons/uses_per_customer",
        "amasty_advancedreview/coupons/min_order",
    ];

    /**
     * @var CustomerGroupChecker
     */
    private $customerGroupChecker;

    /**
     * @var SalesRuleProvider
     */
    private $salesRuleProvider;

    /**
     * AdvancedRuleObserver constructor.
     * @param CustomerGroupChecker $customerGroupChecker
     * @param SalesRuleProvider $salesRuleProvider
     */
    public function __construct(
        CustomerGroupChecker $customerGroupChecker,
        SalesRuleProvider $salesRuleProvider
    ) {
        $this->customerGroupChecker = $customerGroupChecker;
        $this->salesRuleProvider = $salesRuleProvider;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();

        if ($this->customerGroupChecker->isChanged() ||
            array_intersect($event->getChangedPaths(), self::DEPENDS_SALES_RULE_FIELD)
        ) {
            $this->salesRuleProvider->initWebsiteRule((int) $event->getWebsite() ?? null);
        }
    }
}
