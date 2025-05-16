<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Analytics;

use Amasty\Xnotif\Api\Analytics\Daily\StockRepositoryInterface as DailyRepositoryInterface;
use Amasty\Xnotif\Api\Analytics\StockRepositoryInterface;
use Amasty\Xnotif\Model\Analytics\DefaultDataCollector\DefaultData;
use Amasty\Xnotif\Model\Analytics\Request\Daily\Stock as DailyStock;
use Amasty\Xnotif\Model\Analytics\Request\Daily\StockFactory as DailyStockFactory;
use Amasty\Xnotif\Model\ResourceModel\Stock\OrderTotals as OrderTotalsResource;
use Amasty\Xnotif\Model\ResourceModel\Stock\Subscription\CollectionFactory as SubscriptionFactory;
use Magento\Framework\App\ObjectManager;

class Collector
{
    public const ACTION_SUBSCRIBED = 'subscribed';

    public const ACTION_SENT = 'sent';

    /**
     * @var Request\StockFactory
     */
    private $requestStockFactory;

    /**
     * @var DailyStockFactory
     */
    private $dailyStockFactory;

    /**
     * @var SubscriptionFactory
     */
    private $subscriptionFactory;

    /**
     * @var StockRepositoryInterface
     */
    private $stockRepository;

    /**
     * @var DailyRepositoryInterface
     */
    private $dailyRepository;

    /**
     * @var DefaultData
     */
    private $defaultData;

    /**
     * @var OrderTotalsResource
     */
    private $orderTotalsResource;

    public function __construct(
        ?SubscriptionFactory $subscriptionFactory, // @deprecated
        Request\StockFactory $requestStockFactory,
        DailyStockFactory $dailyStockFactory,
        StockRepositoryInterface $stockRepository,
        DailyRepositoryInterface $dailyRepository,
        DefaultData $defaultData,
        OrderTotalsResource $orderTotalsResource = null // TODO move to not optional
    ) {
        $this->requestStockFactory = $requestStockFactory;
        $this->dailyStockFactory = $dailyStockFactory;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->stockRepository = $stockRepository;
        $this->dailyRepository = $dailyRepository;
        $this->defaultData = $defaultData;
        // OM for backward compatibility
        $this->orderTotalsResource = $orderTotalsResource
            ?? ObjectManager::getInstance()->get(OrderTotalsResource::class);
    }

    public function execute()
    {
        if ($this->defaultData->isCollected()) {
            $this->collectStock();
        }
    }

    public function collectStock()
    {
        /** @var DailyStock $dailyStock */
        $dailyStock = $this->dailyStockFactory->create()
            ->loadPrevious();

        if ($dailyStock->getId()) {
            $collectedStock = $this->requestStockFactory->create()
                ->setSubscribed($dailyStock->getSubscribed())
                ->setSent($dailyStock->getSent())
                ->setDate($dailyStock->getDate())
                ->setOrders($this->collectOrders($dailyStock->getDate()));

            $this->stockRepository->save($collectedStock);
        }
    }

    /**
     * @param string $action
     * @param int $increment
     */
    public function updateDaily($action, $increment = 1)
    {
        if (!$this->defaultData->isCollected()) {
            return;
        }

        /** @var DailyStock $dailyStock */
        $dailyStock = $this->dailyStockFactory->create()
            ->loadCurrent();

        $dailyStock->setData(
            $action,
            $dailyStock->getData($action) + $increment
        );
        $dailyStock->updateDate();

        $this->dailyRepository->save($dailyStock);
    }

    /**
     * @param string $date
     *
     * @return string
     */
    private function collectOrders($date)
    {
        return $this->orderTotalsResource->getTotals($date);
    }
}
