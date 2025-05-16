<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\Rule\Quote\Address;

use Magento\Customer\Model\Data\Group;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Item;

class CustomerGroupProvider
{
    /**
     * @param RateRequest $request
     * @return int
     */
    public function getCustomerGroupId(RateRequest $request): int
    {
        $groupId = Group::NOT_LOGGED_IN_ID;
        /** @var Item $firstItem */
        $firstItem = current($request->getAllItems());

        if ($firstItem->getQuote()->getCustomerId()) {
            $groupId = (int)$firstItem->getQuote()->getCustomer()->getGroupId();
        }

        return $groupId;
    }
}
