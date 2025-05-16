<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Controller\Stock;

class Index extends \Amasty\Xnotif\Controller\AbstractIndex
{
    public const TYPE = "stock";

    /**
     * @return bool
     */
    protected function isActive()
    {
        return $this->productAlertHelper->isStockAlertAllowed();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTitle()
    {
        return __("My Back in Stock Subscriptions");
    }
}
