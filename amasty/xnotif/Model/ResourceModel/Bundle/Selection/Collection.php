<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\ResourceModel\Bundle\Selection;

class Collection extends \Magento\Bundle\Model\ResourceModel\Selection\Collection
{
    /**
     * @param bool $printQuery
     * @param bool $logQuery
     *
     * @return $this|\Magento\Bundle\Model\ResourceModel\Selection\Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        /*remove in stock filter*/
        $select = $this->getSelect();
        $where = $select->getPart('where');
        foreach ($where as $i => $item) {
            if (strpos($item, 'stock_status_index.stock_status = 1') !== false) {
                unset($where[$i]);
            }
        }
        $select->setPart('where', $where);

        parent::load($printQuery, $logQuery);

        return $this;
    }
}
