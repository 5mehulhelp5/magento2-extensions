<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Reports Base for Magento 2
 */

namespace Amasty\Reports\Model\ResourceModel\Customers\Returning\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * @var \Amasty\Reports\Model\ResourceModel\Customers\Returning\Collection
     */
    private $filterApplier;

    /**
     * @var array|null
     */
    private $totals = null;

    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        \Amasty\Reports\Model\ResourceModel\Customers\Returning\Collection $filterApplier,
        $mainTable = 'sales_order',
        $resourceModel = \Amasty\Reports\Model\ResourceModel\Customers\Returning\Collection::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);

        $this->filterApplier = $filterApplier;
        $filterApplier->prepareCollection($this, 'main_table');
    }

    /**
     * @return \Magento\Framework\DB\Select
     * @throws \Zend_Db_Select_Exception
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = $this->getConnection()->select();
        $countSelect->from(
            ['returning' => $this->getSelect()->limit(null)],
            ['count' => new \Zend_Db_Expr('COUNT(*)')]
        );

        return $countSelect;
    }

    public function getTotals(): array
    {
        if ($this->totals === null) {
            $this->totals = $this->filterApplier->getTotals($this);
        }

        return $this->totals;
    }
}
