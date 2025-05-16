<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Observer\System;

use Amasty\AdvancedReview\Helper\Config as ScopeConfig;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SlipOldConfigValueObserver implements ObserverInterface
{

    /**
     * @var CustomerGroupChecker
     */
    private $customerGroupChecker;

    /**
     * @var ScopeConfig
     */
    private $scopeConfig;

    /**
     * SlipOldConfigValueObserver constructor.
     * @param CustomerGroupChecker $customerGroupChecker
     * @param ScopeConfig $scopeConfig
     */
    public function __construct(
        CustomerGroupChecker $customerGroupChecker,
        ScopeConfig $scopeConfig
    ) {
        $this->customerGroupChecker = $customerGroupChecker;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $this->customerGroupChecker->setCustomerGroup($this->scopeConfig->getCustomerGroups());
    }
}
