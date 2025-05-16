<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Reports Base for Magento 2
 */

namespace Amasty\Reports\Model\ResourceModel\Report;

use Amasty\Reports\Model\ResourceModel\Filters\AddFromFilter;
use Amasty\Reports\Model\ResourceModel\Filters\AddToFilter;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\Timezone\Validator;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Reports\Model\FlagFactory;
use Magento\Reports\Model\ResourceModel\Helper;
use Magento\Sales\Model\ResourceModel\Report\AbstractReport;
use Psr\Log\LoggerInterface;
use Magento\Sales\Model\Order;

class Dashboard extends AbstractReport
{
    /**
     * @var \Amasty\Reports\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Visitor\CollectionFactory
     */
    private $visitorCollection;

    /**
     * @var AddFromFilter
     */
    private $addFromFilter;

    /**
     * @var AddToFilter
     */
    private $addToFilter;

    public function __construct(
        Context $context,
        LoggerInterface $logger,
        TimezoneInterface $localeDate,
        FlagFactory $reportsFlagFactory,
        \Amasty\Reports\Helper\Data $dataHelper,
        Validator $timezoneValidator,
        DateTime $dateTime,
        ?Product $productResource,//@deprecated
        ?Collection $productCollection,//@deprecated
        ?\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,//@deprecated
        ?Helper $resourceHelper,//@deprecated
        ?\Amasty\Reports\Helper\Data $helper,//@deprecated
        \Magento\Customer\Model\ResourceModel\Visitor\CollectionFactory $visitorCollection,
        $connectionName = null,
        AddFromFilter $addFromFilter = null, // TODO move it to not optional
        AddToFilter $addToFilter = null
    ) {
        parent::__construct(
            $context,
            $logger,
            $localeDate,
            $reportsFlagFactory,
            $timezoneValidator,
            $dateTime,
            $connectionName
        );
        $this->dateTime = $dateTime;
        $this->dataHelper = $dataHelper;
        $this->visitorCollection = $visitorCollection;
        // OM for backward compatibility
        $this->addFromFilter = $addFromFilter ?? ObjectManager::getInstance()->get(AddFromFilter::class);
        $this->addToFilter = $addToFilter ?? ObjectManager::getInstance()->get(AddToFilter::class);
    }

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('report_event', 'id');
    }

    public function getFunnel(?string $from = null, ?string $to = null): array
    {
        $productViewedIds = $this->getViewedProducts();
        $productAddedIds = $this->getAddedProducts();
        $placedOrdersCount = $this->getPlacedOrdersCount();
        $uniqueVisitors = $this->getUniqueCustomerVisits();
        $lostOrdersCount = $uniqueVisitors - $placedOrdersCount;
        $productViewed = count($productViewedIds);
        $addedCount = count($productAddedIds);
        $orderedProducts = $this->getOrderedProducts();
        $orderedCount= count($orderedProducts);
        $notViewed = count(array_diff($productViewedIds, $productAddedIds));
        $viewedCount = $productViewed - $notViewed;
        $viewedPercent = $this->getPercent($viewedCount, $notViewed);
        $abandoned = count(array_diff($productAddedIds, $orderedProducts));
        $addedPercent = $this->getPercent($abandoned, $orderedCount);
        $ordersPercent = $this->getOrdersPercent($placedOrdersCount, $uniqueVisitors);

        return [
            'productViewed' => $productViewed,
            'viewedCount' => round($viewedCount),
            'addedCount' => $addedCount,
            'orderedCount' => $orderedCount,
            'notViewed' => $notViewed,
            'viewedPercent' => round($viewedPercent),
            'addedPercent' => round($addedPercent),
            'abandoned' => $abandoned,
            'ordersPercent' => round($ordersPercent),
            'uniqueVisitors' => $uniqueVisitors,
            'placedOrdersCount' => $placedOrdersCount,
            'lostOrdersCount' => $lostOrdersCount,
        ];
    }

    /**
     * @param int $firstValue
     * @param int $secondValue
     * @return float
     */
    private function getPercent(int $firstValue, int $secondValue): float
    {
        $result = 0;
        $sumValues = $secondValue + $firstValue;

        if ($sumValues !== 0) {
            $result = $firstValue / $sumValues * 100;
        }

        return $result;
    }

    private function getPlacedOrdersCount(): int
    {
        $select = $this->getConnection()->select();

        $select->from(['main_table' => $this->getTable('sales_order')], [])
            ->where(
                'main_table.state NOT IN(?)',
                [Order::STATE_CANCELED, Order::STATE_CLOSED]
            )
            ->where('main_table.remote_ip IS NOT NULL')
            ->columns(['total_orders' => 'COUNT(DISTINCT main_table.entity_id)']);

        $this->addFromFilter->execute($select, 'created_at', 'main_table', null, 'funnel_from');
        $this->addToFilter->execute($select, 'created_at', 'main_table', null, 'funnel_to');

        return (int)$this->getConnection()->fetchOne($select);
    }

    private function getUniqueCustomerVisits(): int
    {
        $visitorsCollection = $this->visitorCollection->create();
        $visitorsCollection->getSelect()
            ->columns(['visitors' => new \Zend_Db_Expr('COUNT(DISTINCT IFNULL(customer_id, visitor_id))')]);

        $this->addFromFilter->execute($visitorsCollection, 'last_visit_at', 'main_table', null, 'funnel_from');
        $this->addToFilter->execute($visitorsCollection, 'last_visit_at', 'main_table', null, 'funnel_to');

        $connection = $visitorsCollection->getConnection();

        return (int)$connection->fetchRow($visitorsCollection->getSelect())['visitors'];
    }

    protected function getViewedProducts(): array
    {
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            ['source_table' => $this->getTable('report_event')],
            ['object_id']
        )->where(
            'source_table.event_type_id = ?',
            \Magento\Reports\Model\Event::EVENT_PRODUCT_VIEW
        );
        if ($storeId = $this->dataHelper->getCurrentStoreId()) {
            $select->where('store_id = ?', $storeId);
        }
        $select->group('source_table.object_id');

        $this->addFromFilter->execute($select, 'logged_at', 'source_table', null, 'funnel_from');
        $this->addToFilter->execute($select, 'logged_at', 'source_table', null, 'funnel_to');

        return $connection->fetchCol($select);
    }

    protected function getAddedProducts(): array
    {
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            ['source_table' => $this->getTable('report_event')],
            ['object_id']
        )->where(
            'source_table.event_type_id = ?',
            \Magento\Reports\Model\Event::EVENT_PRODUCT_TO_CART
        );
        if ($storeId = $this->dataHelper->getCurrentStoreId()) {
            $select->where('store_id = ?', $storeId);
        }
        $select->group('source_table.object_id');

        $this->addFromFilter->execute($select, 'logged_at', 'source_table', null, 'funnel_from');
        $this->addToFilter->execute($select, 'logged_at', 'source_table', null, 'funnel_to');

        return $connection->fetchCol($select);
    }

    protected function getOrderedProducts(): array
    {
        $connection = $this->getConnection();
        $excludedStates = [Order::STATE_CANCELED, Order::STATE_CLOSED];
        $select = $connection->select();
        $select->from(
            ['source_table' => $this->getTable('sales_order_item')],
            ['product_id']
        )->joinInner(
            ['order_table' => $this->getTable('sales_order')],
            "order_table.entity_id = source_table.order_id"
            . " AND order_table.state NOT IN('" . implode("','", $excludedStates) . "')"
            . " AND order_table.remote_ip IS NOT NULL",
            []
        )->where(
            'source_table.parent_item_id IS NULL'
        );
        if ($storeId = $this->dataHelper->getCurrentStoreId()) {
            $select->where('source_table.store_id = ?', $storeId);
        }
        $this->addFromFilter->execute($select, 'created_at', 'order_table', null, 'funnel_from');
        $this->addToFilter->execute($select, 'created_at', 'order_table', null, 'funnel_to');

        return $connection->fetchCol($select);
    }

    /**
     * @param int $placedOrdersCount
     * @param int $uniqueVisitors
     * @return float
     */
    private function getOrdersPercent(int $placedOrdersCount, int $uniqueVisitors): float
    {
        return $uniqueVisitors == 0 ? 0 : ($placedOrdersCount / $uniqueVisitors) * 100;
    }
}
