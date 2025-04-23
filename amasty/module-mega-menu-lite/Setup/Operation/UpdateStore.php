<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Setup\Operation;

use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use \Magento\Framework\Setup\SchemaSetupInterface;

class UpdateStore
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Position\CollectionFactory
     */
    private $positionCollectionFactory;

    public function __construct(
        \Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position\CollectionFactory $positionCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->positionCollectionFactory = $positionCollectionFactory;
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable(Position::TABLE);
        $connection =  $setup->getConnection();

        $connection->addColumn(
            $tableName,
            Position::STORE_VIEW,
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Store View',
            ]
        );

        $this->createNewIndex($connection, $tableName, $setup);
    }

    /**
     * @param AdapterInterface $connection
     * @param string $tableName
     */
    public function splitDataByStores(AdapterInterface $connection, string $tableName)
    {
        $rows = $connection->fetchAll($connection->select()->from($tableName));
        $insertData = [];
        $stores = $this->storeManager->getStores();

        foreach ($rows as $row) {
            foreach ($stores as $store) {
                if (isset($row[Position::ROOT_CATEGORY_ID])
                    && $row[Position::ROOT_CATEGORY_ID] == $store->getRootCategoryId()
                ) {
                    $row[Position::STORE_VIEW] = $store->getId();
                    $insertData[] = $row;
                }
            }
        }

        if ($insertData) {
            $connection->insertOnDuplicate($tableName, $insertData);
        }
    }

    /**
     * @param AdapterInterface $connection
     * @param string $tableName
     * @param SchemaSetupInterface $setup
     */
    private function createNewIndex(AdapterInterface $connection, string $tableName, SchemaSetupInterface $setup)
    {
        $connection->dropIndex(
            $tableName,
            $connection->getIndexName(
                $setup->getTable(Position::TABLE),
                [Position::ROOT_CATEGORY_ID, Position::TYPE, Position::ENTITY_ID],
                AdapterInterface::INDEX_TYPE_UNIQUE
            )
        );
        $this->splitDataByStores($connection, $tableName);

        $connection->dropColumn($tableName, Position::ROOT_CATEGORY_ID);
        $connection->addIndex(
            $tableName,
            $setup->getIdxName(
                $setup->getTable(Position::TABLE),
                [Position::TYPE, Position::ENTITY_ID, Position::STORE_VIEW],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [Position::TYPE, Position::ENTITY_ID, Position::STORE_VIEW],
            AdapterInterface::INDEX_TYPE_UNIQUE
        );
    }
}
