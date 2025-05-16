<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Reports Base for Magento 2
 */

namespace Amasty\Reports\Model\ResourceModel\Report\Order;

use Amasty\Reports\Model\ResourceModel\Filters\AddFromFilter;
use Amasty\Reports\Model\ResourceModel\Filters\AddToFilter;
use Amasty\Reports\Model\Utilities\GetLocalDate;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Helper;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\Order\Config;
use Magento\Sales\Model\ResourceModel\Report\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Collection extends \Magento\Reports\Model\ResourceModel\Order\Collection
{
    /**
     * @var AddFromFilter
     */
    private $addFromFilter;

    /**
     * @var AddToFilter
     */
    private $addToFilter;

    public function __construct(
        EntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Snapshot $entitySnapshot,
        Helper $coreResourceHelper,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        TimezoneInterface $localeDate,
        Config $orderConfig,
        OrderFactory $reportOrderFactory,
        AdapterInterface $connection = null,
        AbstractDb $resource = null,
        AddFromFilter $addFromFilter = null, // TODO move it to not optional
        AddToFilter $addToFilter = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $entitySnapshot,
            $coreResourceHelper,
            $scopeConfig,
            $storeManager,
            $localeDate,
            $orderConfig,
            $reportOrderFactory,
            $connection,
            $resource
        );

        // OM for backward compatibility
        $this->addFromFilter = $addFromFilter ?? ObjectManager::getInstance()->get(AddFromFilter::class);
        $this->addToFilter = $addToFilter ?? ObjectManager::getInstance()->get(AddToFilter::class);
    }

    /**
     * Add period filter by created_at attribute
     *
     * @param string|null $period
     *
     * @return \Amasty\Reports\Model\ResourceModel\Report\Order\Collection
     *
     * @throws \Exception
     */
    public function addCreateAtFilter($period = null)
    {
        $date = new \DateTime();
        $date->setTime(0, 0, 0);
        $this->addFromFilter->execute($this, 'created_at', 'main_table', $date->format('Y-m-d'));
        $date->modify('+1 day');
        $this->addToFilter->execute($this, 'created_at', 'main_table', $date->format('Y-m-d'));

        return $this;
    }
}
