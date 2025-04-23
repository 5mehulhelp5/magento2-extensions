<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 * @noinspection PhpDeprecationInspection
 * @noinspection PhpUndefinedClassInspection
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Model\ResourceModel\IntsPost;

use Magento\Catalog\Model\Product;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Registry;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Olegnax\InstagramFeedPro\Model\ResourceModel\IntsPost;
use Psr\Log\LoggerInterface;

class Collection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'intspost_id';
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var int
     */
    protected $_storeId;
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Collection constructor.
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param Registry $coreRegistry
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        Registry $coreRegistry,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Add store availability filter. Include availability product
     * for store website
     *
     * @param null|string|int|Store $store
     * @return Collection
     * @throws NoSuchEntityException
     * @noinspection PhpParamsInspection
     */
    public function addStoreFilter($store = null)
    {
        if ($store === null) {
            $store = $this->getStoreId();
        }
        if ($store instanceof Store) {
            $store = [$store->getId()];
        }

        if (!is_array($store)) {
            $store = [$store];
        }
        $store[] = Store::DEFAULT_STORE_ID;
        $store = array_map('intval', $store);
        $store = array_unique($store, SORT_NUMERIC);

        if (!empty($store)) {
            $this->addFilter(IntsPost::RELATION_FIELD_STORE, ['in' => $store], 'public');
        }

        return $this;
    }

    /**
     * Return current store id
     *
     * @return int
     * @throws NoSuchEntityException
     */
    public function getStoreId()
    {
        if ($this->_storeId === null) {
            $this->setStoreId($this->_storeManager->getStore()->getId());
        }
        return $this->_storeId;
    }

    /**
     * Set store scope
     *
     * @param int|string|StoreInterface $storeId
     * @return Collection
     */
    public function setStoreId($storeId)
    {
        if ($storeId instanceof StoreInterface) {
            $storeId = $storeId->getId();
        }
        $this->_storeId = (int)$storeId;
        return $this;
    }

    /**
     * @param null|Product|int $products
     * @return $this
     * @noinspection PhpParamsInspection
     */
    public function addRelatedProductFilter($products = null)
    {
        if ($products === null) {
            /** @var Product $products */
            $products = $this->_coreRegistry->registry('current_product');
        }
        if ($products instanceof Product) {
            $products = [$products->getId()];
        }
        if (!is_array($products)) {
            $products = [$products];
        }

        $products = array_map('intval', $products);
        $products = array_unique($products, SORT_NUMERIC);
        $products = array_filter($products);

        if (!empty($products)) {
            $this->addFilter(IntsPost::RELATION_FIELD_PRODUCT, ['in' => $products], 'public');
        }

        return $this;
    }

    /**
     * Hook for operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->joinRelationTable(IntsPost::RELATION_TABLE_STORE, IntsPost::RELATION_FIELD_STORE);
        $this->joinRelationTable(IntsPost::RELATION_TABLE_PRODUCT, IntsPost::RELATION_FIELD_PRODUCT);
        parent::_renderFiltersBefore();
    }

    /**
     * @param string $tableName
     * @param string $filterField
     */
    private function joinRelationTable($tableName, $filterField)
    {
        if ($this->getFilter($filterField)) {
            $linkField = IntsPost::IDENTIFIER_FIELD;
            $_tableName = $this->getTable($tableName);
            $this->getSelect()->join(
                [$_tableName => $_tableName],
                'main_table.' . $linkField . ' = ' . $_tableName . '.' . $linkField,
                []
            )->group(
                'main_table.' . $linkField
            );
        }
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Olegnax\InstagramFeedPro\Model\IntsPost::class,
            IntsPost::class
        );
    }
}
