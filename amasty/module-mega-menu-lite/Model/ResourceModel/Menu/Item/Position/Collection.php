<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenuLite\Model\Menu\Item\Position;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position as PositionResource;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\WrapColumns;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;

class Collection extends AbstractCollection
{
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
        $this->_setIdFieldName(PositionResource::ID);
        $this->_init(
            Position::class,
            PositionResource::class
        );
    }

    /**
     * @param int $storeId
     * @return Collection
     */
    public function getSortedCollection(int $storeId)
    {
        $this->getSelect()
            ->where(
                sprintf(
                    'main_table.%s = ? AND main_table.%s = "%s"',
                    PositionResource::STORE_VIEW,
                    PositionResource::TYPE,
                    ItemInterface::CATEGORY_TYPE
                ),
                $storeId
            )
            ->orWhere(
                sprintf(
                    'main_table.%s = ? AND main_table.%s = "%s"',
                    PositionResource::STORE_VIEW,
                    PositionResource::TYPE,
                    ItemInterface::CUSTOM_TYPE
                ),
                Store::DEFAULT_STORE_ID
            )
            ->order(PositionResource::POSITION);

        return $this;
    }

    /**
     * @param int $storeId
     * @return void
     */
    public function joinPositionTableByStore(int $storeId): void
    {
        $alias = sprintf('%s_%s', PositionResource::TABLE_ALIAS, $storeId);
        $condition = sprintf(
            '%1$s.%2$s = main_table.%2$s %3$s main_table.%4$s = %1$s.%4$s %3$s %1$s.%5$s = %6$s',
            $alias,
            PositionResource::TYPE,
            Select::SQL_AND,
            PositionResource::ENTITY_ID,
            PositionResource::STORE_VIEW,
            $storeId
        );

        $this->getSelect()->joinLeft(
            [$alias => $this->getTable(PositionResource::TABLE)],
            $condition,
            $this->getColumsForSelect($storeId)
        );
    }

    private function getColumsForSelect(int $storeId): array
    {
        $storeCols = $this->wrapColumns->execute(
            PositionResource::TABLE_ALIAS,
            [
                PositionResource::ID,
                PositionResource::ENTITY_ID,
                PositionResource::STORE_VIEW,
                PositionResource::POSITION
            ],
            $storeId,
            'main_table'
        );

        return $storeCols;
    }

    public function joinLinkTable(): void
    {
        $joinCondition = sprintf(
            'main_table.%s = "%s" AND main_table.%s = links.%s',
            ItemInterface::TYPE,
            ItemInterface::CUSTOM_TYPE,
            ItemInterface::ENTITY_ID,
            LinkInterface::ENTITY_ID
        );

        $this->getSelect()->joinLeft(
            ['links' => $this->getTable(LinkInterface::TABLE_NAME)],
            $joinCondition,
            []
        );
    }

    /**
     * Deprecated because link_type now stored in store table.
     * Can't resolve filter by link_type on this method.
     * @deprecated
     */
    // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    public function addLinkTypeFilter(array $values): void
    {
    }

    public function addDefaultLevelFilter(): void
    {
        $this->getSelect()->where(
            sprintf(
                'coalesce(%1$s, %2$d) = (%2$d)',
                LinkInterface::LEVEL,
                LinkInterface::DEFAULT_LEVEL
            )
        );
    }
}
