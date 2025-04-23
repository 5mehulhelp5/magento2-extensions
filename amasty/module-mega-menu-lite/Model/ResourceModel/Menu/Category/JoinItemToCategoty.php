<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\ResourceModel\Menu\Category;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\WrapColumns;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Store\Model\Store;

class JoinItemToCategoty
{
    /**
     * @var WrapColumns
     */
    private $wrapColumns;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    public function __construct(
        WrapColumns $wrapColumns,
        MetadataPool $metadataPool
    ) {
        $this->wrapColumns = $wrapColumns;
        $this->metadataPool = $metadataPool;
    }

    public function execute(
        CategoryCollection $collection,
        ?array $fields = null,
        ?int $storeId = Store::DEFAULT_STORE_ID
    ): void {
        if ($storeId === Store::DEFAULT_STORE_ID) {
            $this->joinItemByStore($collection, $fields, $storeId);
            $this->joinPositionByStore($collection, $storeId);
        } else {
            $this->joinItemByStore($collection, [], Store::DEFAULT_STORE_ID);
            $this->joinItemByStore($collection, $fields, $storeId);
            $this->joinPositionByStore($collection, Store::DEFAULT_STORE_ID);
            $this->joinPositionByStore($collection, $storeId);
        }
    }

    private function joinItemByStore(
        CategoryCollection $collection,
        ?array $fields,
        ?int $storeId
    ): void {
        $table = $collection->getTable(ItemInterface::TABLE_NAME);
        $tableAlias = sprintf('%s_%s', ItemInterface::TABLE_NAME, $storeId);
        $fields = $this->wrapColumns->execute(ItemInterface::TABLE_NAME, $fields, $storeId);
        $condition = sprintf(
            '%1$s.%2$s = "%3$s" AND e.%4$s = %1$s.%5$s AND %1$s.%6$s = %7$s',
            $tableAlias,
            ItemInterface::TYPE,
            ItemInterface::CATEGORY_TYPE,
            $this->getCategoryIdentifier(),
            ItemInterface::ENTITY_ID,
            ItemInterface::STORE_ID,
            $storeId
        );

        $collection->getSelect()->joinLeft([$tableAlias => $table], $condition, $fields);
    }

    private function joinPositionByStore(CategoryCollection $collection, ?int $storeId): void
    {
        $table = $collection->getTable(Position::TABLE);
        $tableAlias = sprintf('%s_%s', Position::TABLE_ALIAS, $storeId);
        $fields = $this->wrapColumns->execute(Position::TABLE_ALIAS, [Position::POSITION], $storeId);
        $condition = sprintf(
            'e.%s = %s.%s AND %s.%s = "%s" AND %s.%s = "%s"',
            ItemInterface::ENTITY_ID,
            $tableAlias,
            ItemInterface::ENTITY_ID,
            $tableAlias,
            ItemInterface::TYPE,
            ItemInterface::CATEGORY_TYPE,
            $tableAlias,
            Position::STORE_VIEW,
            $storeId
        );

        $collection->getSelect()->joinLeft([$tableAlias => $table], $condition, $fields);
    }

    private function getCategoryIdentifier(): string
    {
        return (string) $this->metadataPool->getMetadata(CategoryInterface::class)->getLinkField();
    }
}
