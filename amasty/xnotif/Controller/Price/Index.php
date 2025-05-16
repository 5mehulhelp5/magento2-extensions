<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Controller\Price;

class Index extends \Amasty\Xnotif\Controller\AbstractIndex
{
    public const TYPE = "price";

    /**
     * @return bool
     */
    protected function isActive()
    {
        return $this->config->allowForCurrentCustomerGroup('price')
            && $this->productAlertHelper->isPriceAlertAllowed();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTitle()
    {
        return __("My Price Subscriptions");
    }
}
