<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Analytics;

use Amasty\Xnotif\Api\Analytics\Data\StockInterface as Stock;
use Amasty\Xnotif\Model\Analytics\DefaultDataCollector\DefaultData;
use Amasty\Xnotif\Model\ResourceModel\Stock\Subscription\Collection;
use Amasty\Xnotif\Model\ResourceModel\Stock\Subscription\CollectionFactory as StockCollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;

class DefaultDataCollector
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var StockCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var DefaultData
     */
    private $defaultData;

    public function __construct(
        ResourceConnection $resourceConnection,
        StockCollectionFactory $collectionFactory,
        DefaultData $defaultData
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->collectionFactory = $collectionFactory;
        $this->defaultData = $defaultData;
    }

    public function execute(): void
    {
        if ($this->defaultData->isCollected()) {
            return;
        }

        $connection = $this->resourceConnection->getConnection();
        $stockAnalyticsTable = $this->resourceConnection->getTableName(Stock::MAIN_TABLE);
        $analyticDataSelect = $connection->select()->from($stockAnalyticsTable)->limit(1);
        if ($connection->fetchCol($analyticDataSelect)) {
            $this->defaultData->markAsCollected();
            return;
        }

        $stockSubscriptions = $this->collectionFactory->create();

        $statistics = array_merge_recursive(
            $this->getCountByDate(clone $stockSubscriptions->getSelect(), 'add_date', 'subscribed'),
            $this->getCountByDate(clone $stockSubscriptions->getSelect(), 'send_date', 'sent'),
            $this->getOrders($stockSubscriptions)
        );

        foreach ($statistics as &$statistic) {
            if (is_array($statistic['date'])) {
                $statistic['date'] = $statistic['date'][0];
            }
            if (!isset($statistic['subscribed'])) {
                $statistic['subscribed'] = 0;
            }
            if (!isset($statistic['sent'])) {
                $statistic['sent'] = 0;
            }
            if (!isset($statistic['orders'])) {
                $statistic['orders'] = 0;
            }
        }

        if ($statistics) {
            $connection->insertMultiple(
                $stockAnalyticsTable,
                $statistics
            );
        }

        $this->defaultData->markAsCollected();
    }

    private function getCountByDate(Select $select, string $fieldDate, string $alias): array
    {
        $select
            ->reset(Select::COLUMNS)
            ->columns('count(*) as ' . $alias)
            ->columns('DATE(`' . $fieldDate . '`) as date')
            ->group('date')
            ->having('`date` IS NOT NULL');
        $result = $this->resourceConnection->getConnection()->fetchAll($select);

        return $this->updateResult($result);
    }

    private function getOrders(Collection $stockSubscriptions): array
    {
        $stockSubscriptions
            ->_renderFiltersBefore()
            ->joinSales(false)
            ->getSelect()
            ->reset(Select::COLUMNS)
            ->columns('DATE(sales.created_at) as date')
            ->columns('SUM(sales_item.base_row_total) as orders')
            ->group('date');
        $result = $this->resourceConnection->getConnection()->fetchAll($stockSubscriptions->getSelect());

        return $this->updateResult($result);
    }

    private function updateResult(array $result): array
    {
        foreach ($result as $key => $data) {
            if (isset($data['date'])) {
                $result[$data['date']] = $data;
                unset($result[$key]);
            }
        }

        return $result;
    }
}
