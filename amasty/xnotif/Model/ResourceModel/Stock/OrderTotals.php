<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\ResourceModel\Stock;

use Amasty\Xnotif\Model\ResourceModel\Stock\Subscription\CollectionFactory as StockCollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;

class OrderTotals
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var StockCollectionFactory
     */
    private $stockCollectionFactory;

    public function __construct(
        ResourceConnection $resourceConnection,
        StockCollectionFactory $stockCollectionFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->stockCollectionFactory = $stockCollectionFactory;
    }

    public function getTotals(string $date): ?string
    {
        $select = $this->resourceConnection->getConnection()->select();
        $this->initSelect($select);
        $this->joinSales($select);

        return $this->resourceConnection->getConnection()->fetchOne($select, ['date' => $date]);
    }

    public function initSelect(Select $select): void
    {
        $select->from(
            ['main_table' => $this->resourceConnection->getTableName('product_alert_stock')],
            []
        );
        $select->joinLeft(
            ['customer' => $this->resourceConnection->getTableName('customer_entity')],
            'customer.entity_id = main_table.customer_id',
            []
        );
    }

    public function joinSales(Select $select, bool $daily = true): void
    {
        $salesCond = new \Zend_Db_Expr(
            '(sales.customer_email = main_table.email OR sales.customer_email = customer.email)'
            . ' AND `main_table`.`send_date` < `sales`.`created_at`'
        );
        if ($daily) {
            $salesCond .= ' AND `sales`.`created_at` >= :date AND `sales`.`created_at` < :date + INTERVAL 1 DAY';
        }

        $select
            ->join(
                ['sales' => $this->resourceConnection->getTableName('sales_order')],
                $salesCond,
                ['']
            )
            ->join(
                ['sales_item' => $this->resourceConnection->getTableName('sales_order_item')],
                'sales.entity_id = sales_item.order_id AND (sales_item.product_id = main_table.product_id'
                . ' OR sales_item.product_id = main_table.parent_id)',
                ['totals' => 'SUM(sales_item.base_row_total)']
            );
    }
}
