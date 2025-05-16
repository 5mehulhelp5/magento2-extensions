<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\ResourceModel\Analytics\Request\Daily\Stock;

use Amasty\Xnotif\Model\Analytics\Request\Daily\Stock;
use Amasty\Xnotif\Model\ResourceModel\Analytics\Request\Daily\Stock as StockResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 */
class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Stock::class, StockResource::class);
    }
}
