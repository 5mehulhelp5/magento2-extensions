<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\ResourceModel\Analytics\Request\Daily;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Amasty\Xnotif\Api\Analytics\Data\Daily\StockInterface;

/**
 * Class Stock
 */
class Stock extends AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(StockInterface::MAIN_TABLE, StockInterface::ID);
    }
}
