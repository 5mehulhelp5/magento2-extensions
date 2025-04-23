<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Model\ResourceModel\CategoryCollectionFactory;
use Magento\Catalog\Model\Category;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Position extends AbstractDb
{
    public const TABLE = 'amasty_menu_item_order';
    public const TABLE_ALIAS = 'item_order';
    public const ID = 'id';
    public const TYPE = 'type';
    public const POSITION = 'sort_order';
    public const ROOT_CATEGORY_ID = 'root_category_id';
    public const ENTITY_ID = 'entity_id';
    public const STORE_VIEW = 'store_view';

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CategoryCollectionFactory $categoryCollectionFactory,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE, self::ID);
    }

    /**
     * @param \Amasty\MegaMenuLite\Model\Menu\Item\Position $item
     * @param int $afterItemId
     * @return int|string
     */
    public function changePosition($item, $afterItemId)
    {
        $table = $this->getTable(self::TABLE);
        $connection = $this->getConnection();
        $positionField = $connection->quoteIdentifier(self::POSITION);

        $bind = [self::POSITION => new \Zend_Db_Expr($positionField . ' - 1')];
        $where = [
            self::STORE_VIEW . ' = ?' => $item->getStoreView(),
            $positionField . ' > ?' => $item->getSortOrder(),
        ];
        $connection->update($table, $bind, $where);

        /**
         * Prepare position value
         */
        if ($afterItemId) {
            $select = $connection->select()->from($table, self::POSITION)->where(self::ID . ' = :id');
            $position = $connection->fetchOne($select, [self::ID => $afterItemId]);
            $position++;
        } else {
            $position = 0;
        }

        $bind = [self::POSITION => new \Zend_Db_Expr($positionField . ' + 1')];
        $where = [
            self::STORE_VIEW . ' = ?' => $item->getStoreView(),
            $positionField . ' >= ?' => $position
        ];
        $connection->update($table, $bind, $where);

        $data = [
            self::POSITION => $position
        ];
        $connection->update($table, $data, [self::ID . ' = ?' => $item->getId()]);

        return $position;
    }

    /**
     * @param int|null $store
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function importCategoryPositions($store = null)
    {
        $categories = $this->categoryCollectionFactory->create()->getIncludedInMenuCategories($store);
        $data = [];
        $entityIds = [];
        $rootCategoryId = $this->storeManager->getStore($store)->getRootCategoryId();

        /** @var Category $category */
        foreach ($categories as $category) {
            if ($category->getData('parent_id') == $rootCategoryId) {
                $entityIds[] = $category->getEntityId();
                $data = $this->generateData($category, $data, $store);
            }
        }

        $this->deleteCategories($entityIds, $store);
        if (!empty($data)) {
            $this->getConnection()->insertOnDuplicate(
                $this->getTable(self::TABLE),
                $data,
                [self::ENTITY_ID]
            );
        }
    }

    /**
     * @param Category $category
     * @param array $data
     * @param int|null $store
     * @return array
     */
    private function generateData(Category $category, $data = [], $store = null)
    {
        $data[] = [
            self::ENTITY_ID => $category->getEntityId(),
            self::TYPE => ItemInterface::CATEGORY_TYPE,
            self::POSITION => $category->getPosition(),
            self::STORE_VIEW => $store
        ];

        return $data ?? [];
    }

    private function deleteCategories(array $entityIds, ?int $store = null): void
    {
        if (!empty($entityIds)) {
            $where = [
                'type = ?' => ItemInterface::CATEGORY_TYPE,
                'entity_id NOT IN (?)' => $entityIds
            ];
            if ($store !== null) {
                $where['store_view = ?'] = $store;
            }

            $this->getConnection()->delete(
                $this->getTable(self::TABLE),
                $where
            );
        }
    }

    /**
     * @param $type
     * @param $entityId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteItem($type, $entityId)
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            [self::TYPE . ' = ?' => $type, self::ENTITY_ID . ' = (?)' => $entityId]
        );
    }

    /**
     * @return array|false
     */
    public function getPosition(int $entityId, int $storeId, string $type)
    {
        $select = $this->getConnection()->select()->from($this->getMainTable())
            ->where(sprintf('%s = ?', self::ENTITY_ID), $entityId)
            ->where(sprintf('%s = ?', self::STORE_VIEW), $storeId)
            ->where(sprintf('%s = ?', self::TYPE), $type);

        return $this->getConnection()->fetchRow($select);
    }
}
