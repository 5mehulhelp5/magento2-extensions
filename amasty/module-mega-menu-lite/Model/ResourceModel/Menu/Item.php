<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\ResourceModel\Menu;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Model\ResourceModel\CategoryCollection;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position\GetMaxSortOrder;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;

class Item extends AbstractDb
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var GetMaxSortOrder
     */
    private $getMaxSortOrder;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        StoreManagerInterface $storeManager,
        GetMaxSortOrder $getMaxSortOrder,
        Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->getMaxSortOrder = $getMaxSortOrder;
    }

    /**
     * Initialize table nad PK name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ItemInterface::TABLE_NAME, ItemInterface::ID);
    }

    public function deleteItem(string $type, int $entityId): void
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            [ItemInterface::TYPE . '=?' => $type, ItemInterface::ENTITY_ID . '=?' => $entityId]
        );
    }

    /**
     * @param Category|ItemInterface $object
     * @return AbstractDb
     */
    protected function _afterSave(AbstractModel $object)
    {
        if ($object->getType() === ItemInterface::CATEGORY_TYPE) {
            $this->saveCategorySortOrder($object);
        } else {
            $this->saveCustomSortOrder($object);
        }

        return parent::_afterSave($object);
    }

    private function saveCustomSortOrder(ItemInterface $object): void
    {
        $this->prepareAndInsertData($object, [$object->getStoreId()]);
    }

    private function saveCategorySortOrder(ItemInterface $object): void
    {
        $storeIds = [];
        $category = $this->categoryRepository->get($object->getCategoryId() ?: $object->getEntityId());
        if ($category->getLevel() == CategoryCollection::MENU_LEVEL) {
            $storeIds = $category->getStoreIds();
        }
        if ($object->getSortOrder() === null) {
            $object->setSortOrder((int) $category->getPosition());
        }

        $this->prepareAndInsertData($object, $storeIds);
    }

    private function prepareAndInsertData(ItemInterface $object, array $storeIds): void
    {
        $data = $this->getPositionDataByStore($object, $storeIds);

        if (!empty($data)) {
            $this->getConnection()->insertOnDuplicate(
                $this->getTable(Position::TABLE),
                $data
            );
        }
    }

    private function getPositionDataByStore(ItemInterface $object, array $storeIds): array
    {
        $data = [];
        $entityId = (int)($object->getCategoryId() ?: $object->getEntityId());
        $itemType = $object->getType();
        $storeIds = array_map('intval', $storeIds);

        foreach ($storeIds as $storeId) {
            if ($this->isNeedAddToItemOrderTable($entityId, $storeId, $itemType)) {
                $sortOrder = $itemType === ItemInterface::CUSTOM_TYPE
                    ? $object->getSortOrder()
                    : $this->getMaxSortOrder->execute($storeId);
                $data[] = [
                    Position::STORE_VIEW => $storeId,
                    Position::TYPE => $itemType,
                    Position::POSITION => $sortOrder,
                    Position::ENTITY_ID => $entityId
                ];
            }
        }

        return $data;
    }

    private function isNeedAddToItemOrderTable(int $entityId, int $storeId, string $itemType): bool
    {
        if ($itemType === ItemInterface::CUSTOM_TYPE) {
            return true; // for custom item sort order can be edited from ui form
        }
        $select = $this->getConnection()->select()
            ->from(
                $this->getTable(Position::TABLE),
                Position::ENTITY_ID
            )
            ->where(Position::ENTITY_ID . ' = ?', $entityId)
            ->where(Position::STORE_VIEW . ' = ?', $storeId)
            ->where(Position::TYPE . ' = ?', $itemType);

        return !$this->getConnection()->fetchOne($select);
    }

    /**
     * Fix saving empty value for nullable fields
     *
     * @param DataObject $object
     * @param string $table
     * @return array
     */
    protected function _prepareDataForTable(DataObject $object, $table)
    {
        $preparedData = parent::_prepareDataForTable($object, $table);
        $storeData = $object->getStoredData();

        if (isset($storeData[ItemInterface::STORE_ID]) && $storeData[ItemInterface::STORE_ID]) {
            $nullableFields = $this->getNullableColumns();

            foreach ($nullableFields as $fieldName) {
                if (array_key_exists($fieldName, $preparedData)
                    && $object->getData($fieldName) === ''
                ) {
                    $preparedData[$fieldName] = '';
                }
            }
        }

        return $preparedData;
    }

    public function getNullableColumns(): array
    {
        $columnsInfo = $this->getConnection()->describeTable($this->getMainTable());

        return array_reduce($columnsInfo, function ($nullableColumns, $columnDescription): array {
            if ($columnDescription['NULLABLE'] ?? false) {
                $nullableColumns[] = $columnDescription['COLUMN_NAME'];
            }

            return $nullableColumns;
        }, []);
    }
}
