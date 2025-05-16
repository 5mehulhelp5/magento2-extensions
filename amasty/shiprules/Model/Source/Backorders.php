<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\Source;

use Amasty\CommonRules\Model\Rule as CommonRule;

/**
 * Select source data.
 */
class Backorders
{
    public function toArray()
    {
        return [
            CommonRule::ALL_ORDERS => __('All orders'),
            CommonRule::BACKORDERS_ONLY => __('Backorders only'),
            CommonRule::NON_BACKORDERS => __('Non backorders')
        ];
    }
}
