<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Model\ResourceModel;

use Amasty\XnotifSubscriptionFunctionality\Api\Data\StockAlertInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class StockAlert extends AbstractDb
{
    public const TABLE_NAME = 'amasty_restock_alert';
    public const PRODUCT_ALERT_STOCK_TABLE = 'product_alert_stock';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, StockAlertInterface::ALERT_ID);
    }

    public function updateAlertStatusById(array $alertIds, $status): void
    {
        $this->getConnection()->update(
            $this->getTable(self::PRODUCT_ALERT_STOCK_TABLE),
            ['status' => $status],
            ['alert_stock_id IN (?)' => $alertIds]
        );
    }

    public function getRestockProductIds(): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['a' => $this->getTable(self::PRODUCT_ALERT_STOCK_TABLE)], 'product_id')
            ->joinInner(
                ['r' => $this->getTable(self::TABLE_NAME)],
                'a.alert_stock_id = r.alert_stock_id',
                ''
            )
            ->where('status = 1');

        return array_unique($connection->fetchCol($select));
    }

    public function getAlertIdsByProductIds(array $productIds): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['a' => $this->getTable(self::PRODUCT_ALERT_STOCK_TABLE)], 'alert_stock_id')
            ->joinInner(
                ['r' => $this->getTable(self::TABLE_NAME)],
                'a.alert_stock_id = r.alert_stock_id'
            )
            ->where('status = 1')
            ->where('product_id IN (?)', $productIds);

        return $connection->fetchCol($select);
    }
}
