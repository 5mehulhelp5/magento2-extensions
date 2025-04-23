<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\ResourceModel\Menu\Link;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Model\OptionSource\Status;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\WrapColumns;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface;
use \Magento\Framework\DB\Select;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;

class Collection extends AbstractCollection
{
    public const URL = 'url';

    /**
     * @var WrapColumns
     */
    private $wrapColumns;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        WrapColumns $wrapColumns,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->wrapColumns = $wrapColumns;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_setIdFieldName(LinkInterface::ENTITY_ID);
        $this->_init(
            \Amasty\MegaMenuLite\Model\Menu\Link::class,
            \Amasty\MegaMenuLite\Model\ResourceModel\Menu\Link::class
        );
    }

    public function joinItemContent(?int $storeId = Store::DEFAULT_STORE_ID, ?array $cols = null): void
    {
        if (isset($this->_joinedTables[ItemInterface::TABLE_NAME])) {
            return;
        }

        if ($storeId === Store::DEFAULT_STORE_ID) {
            $this->joinItemContentByStore(Store::DEFAULT_STORE_ID, $cols);
        } else {
            $this->joinItemContentByStore(Store::DEFAULT_STORE_ID, []);
            $this->joinItemContentByStore($storeId, $cols);
        }

        $this->_joinedTables[ItemInterface::TABLE_NAME] = true;
    }

    private function joinItemContentByStore(int $storeId, ?array $cols = null): void
    {
        $alias = sprintf('%s_%s', ItemInterface::TABLE_NAME, $storeId);
        $cols = $this->wrapColumns->execute(ItemInterface::TABLE_NAME, $cols, $storeId);
        $condition = sprintf(
            '%1$s.%2$s = "%3$s" %4$s main_table.%5$s = %1$s.%5$s %4$s %1$s.%6$s = %7$s',
            $alias,
            ItemInterface::TYPE,
            ItemInterface::CUSTOM_TYPE,
            Select::SQL_AND,
            LinkInterface::ENTITY_ID,
            ItemInterface::STORE_ID,
            $storeId
        );

        $this->getSelect()->joinLeft(
            [$alias => $this->getTable(ItemInterface::TABLE_NAME)],
            $condition,
            $cols === null ? Select::SQL_WILDCARD : $cols
        );
    }

    public function joinItemOrder(?int $storeId = Store::DEFAULT_STORE_ID, ?array $cols = null): void
    {
        if (isset($this->_joinedTables[Position::TABLE_ALIAS])) {
            return;
        }
        if (!isset($this->_joinedTables[ItemInterface::TABLE_NAME])) {
            $this->joinItemContent($storeId);
        }

        if ($storeId === Store::DEFAULT_STORE_ID) {
            $this->joinItemOrderByStore(Store::DEFAULT_STORE_ID, $cols);
        } else {
            $this->joinItemOrderByStore(Store::DEFAULT_STORE_ID, []);
            $this->joinItemOrderByStore($storeId, $cols);
        }

        $this->_joinedTables[Position::TABLE_ALIAS] = true;
    }

    public function joinItemOrderByStore(int $storeId, ?array $cols = null): void
    {
        $alias = sprintf('%s_%s', Position::TABLE_ALIAS, $storeId);
        $cols = $this->wrapColumns->execute(Position::TABLE_ALIAS, $cols, $storeId);
        $condition = sprintf(
            'main_table.%s = %s.%s  %s %s.%s = %s %s %s.%s = %s.%s',
            LinkInterface::ENTITY_ID,
            $alias,
            Position::ENTITY_ID,
            Select::SQL_AND,
            $alias,
            Position::STORE_VIEW,
            $storeId,
            Select::SQL_AND,
            sprintf('%s_%s', ItemInterface::TABLE_NAME, Store::DEFAULT_STORE_ID),
            ItemInterface::TYPE,
            $alias,
            Position::TYPE
        );

        $this->getSelect()->joinLeft(
            [$alias => $this->getTable(Position::TABLE)],
            $condition,
            $cols === null ? Select::SQL_WILDCARD : $cols
        );
    }

    public function excludePath(string $path): void
    {
        $this->getSelect()->where(
            sprintf('main_table.%s NOT LIKE ?', LinkInterface::PATH),
            $path . '%'
        );
    }

    public function excludeByEntityId(int $entityId): void
    {
        $this->addFieldToFilter(
            sprintf('%s.%s', 'main_table', LinkInterface::ENTITY_ID),
            ['neq' => $entityId]
        );
    }

    public function addSortOrder(int $storeId): void
    {
        $this->joinItemOrder();
        $wrapedColumns = $this->wrapColumns->execute(Position::TABLE_ALIAS, [Position::POSITION], $storeId);
        $column = array_shift($wrapedColumns);
        $this->setOrder(is_string($column) ? $column : $column->__toString());
    }

    public function addUrlToSelect(int $storeId = Store::DEFAULT_STORE_ID): void
    {
        $fields = $this->wrapColumns->execute(ItemInterface::TABLE_NAME, [ItemInterface::LINK], $storeId);
        $this->getSelect()->columns([self::URL => $fields[ItemInterface::LINK]]);
    }

    public function addFieldsToSelect(): void
    {
        $this->addFieldToSelect(LinkInterface::ENTITY_ID);
        $this->addFieldToSelect(LinkInterface::PARENT_ID);
        $this->addFieldToSelect(LinkInterface::LEVEL);
    }

    public function sortByEntityId(): void
    {
        $this->addOrder(
            sprintf('main_table.%s', LinkInterface::ENTITY_ID),
            Collection::SORT_ORDER_ASC
        );
    }

    public function excludeDisabled(int $storeId): void
    {
        $wrapedColumns = $this->wrapColumns->execute(ItemInterface::TABLE_NAME, [ItemInterface::STATUS], $storeId);
        $column = array_shift($wrapedColumns);
        $this->addFieldToFilter($column, ['neq' => Status::DISABLED]);
    }
}
