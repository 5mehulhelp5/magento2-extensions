<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Plugins\CatalogInventory\Observer;

use Amasty\Xnotif\Model\Notification\LowStockAlert as NotificationModel;
use Magento\CatalogInventory\Observer\ItemsForReindex;
use Magento\CatalogInventory\Observer\SubtractQuoteInventoryObserver;

class LowStockAlert
{
    /**
     * @var NotificationModel
     */
    private $notificationModel;

    /**
     * @var ItemsForReindex
     */
    private $itemsForReindex;

    public function __construct(
        NotificationModel $notificationModel,
        ItemsForReindex $itemsForReindex
    ) {
        $this->notificationModel = $notificationModel;
        $this->itemsForReindex = $itemsForReindex;
    }

    /**
     * @param SubtractQuoteInventoryObserver $subject
     * @param SubtractQuoteInventoryObserver $result
     */
    public function afterExecute($subject, $result)
    {
        $this->notificationModel->notify($this->getCollectionItems());
    }

    /**
     * @return array
     */
    protected function getCollectionItems()
    {
        return $this->itemsForReindex->getItems();
    }
}
