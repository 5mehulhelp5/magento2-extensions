<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\ResourceModel\AdminNotification;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;

class Collection extends ProductCollection
{
    public const STOCK_ALERT_TABLE = 'product_alert_stock';

    public function getCollection(?int $storeId = null)
    {
        $from = $this->_localeDate->date()
            ->setTime(0, 0, 0)
            ->setTimezone(new \DateTimeZone('UTC'))
            ->format('Y-m-d H:i:s');
        $to = $this->_localeDate->date()
            ->setTime(23, 59, 59)
            ->setTimezone(new \DateTimeZone('UTC'))
            ->format('Y-m-d H:i:s');

        $this->addAttributeToSelect('name');
        $this->setStoreId($storeId);

        $alertTable = $this->_resource->getTableName(self::STOCK_ALERT_TABLE);
        $this->getSelect()->joinRight(
            ['s' => $alertTable],
            sprintf('s.product_id = e.entity_id AND s.store_id = %d', $storeId),
            [
                'total_cnt' => 'count(s.product_id)',
                'cnt' => 'COUNT( NULLIF(`s`.`status`, 1) )',
                'last_d' => 'MAX(add_date)',
                'product_id'
            ]
        )->where(
            'add_date >= ?',
            $from
        )->where(
            'add_date <= ?',
            $to
        )->group(['s.product_id']);

        return $this;
    }
}
