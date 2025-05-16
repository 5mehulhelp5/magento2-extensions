<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @deprecated
 * @see \Amasty\Xnotif\Plugins\Sales\Model\Order\Shipment\SendLowStockAlert
 */
class LowStockAlert implements ObserverInterface
{
    /**
     * phpcs:disable Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
    }
}
