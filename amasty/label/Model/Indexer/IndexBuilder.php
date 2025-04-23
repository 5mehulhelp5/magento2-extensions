<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Indexer;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Model\Label;
use Amasty\Label\Model\Label\GetMatchedProductIdsInterface;
use Amasty\Label\Model\ResourceModel\Indexer\ProductTypeDataProvider;
use Amasty\Label\Model\ResourceModel\Label\Collection as LabelCollection;
use Amasty\Label\Model\ResourceModel\Label\CollectionFactory;
use Amasty\Label\Setup\Uninstall;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\CatalogRule\Model\Indexer\IndexerTableSwapperInterface as TableSwapper;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class IndexBuilder
{
    public const PRODUCT_ID = 'product_id';
    public const STORE_ID = 'store_id';

    /**
     * @var bool
     */
    private $isFullIndexRunning = false;

    /**
     * @var LabelCollection|null
     */
    private $fullLabelCollection = null;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var int
     */
    private $batchCount;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var CacheContext
     */
    private $cacheContext;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var int
     */
    private $batchCacheCount;

    /**
     * @var GetMatchedProductIdsInterface
     */
    private $getMatchedProductIds;

    /**
     * @var ProductTypeDataProvider
     */
    private $productTypeDataProvider;

    /**
     * @var TableSwapper
     */
    private $tableSwapper;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var OrderProcessingFlag
     */
    private $orderProcessingFlag;

    public function __construct(
        ResourceConnection $resource,
        LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        ProductRepository $productRepository,
        ProductCollectionFactory $productCollectionFactory,
        CacheContext $cacheContext,
        ManagerInterface $eventManager,
        GetMatchedProductIdsInterface $getMatchedProductIds,
        ProductTypeDataProvider $productTypeDataProvider,
        $batchCount = 1000,
        $batchCacheCount = 100,
        TableSwapper $tableSwapper = null, // TODO move to not optional
        CacheInterface $cache = null, // TODO move to not optional
        OrderProcessingFlag $orderProcessingFlag = null // TODO move to not optional
    ) {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->productRepository = $productRepository;
        $this->batchCount = $batchCount;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->cacheContext = $cacheContext;
        $this->eventManager = $eventManager;
        $this->batchCacheCount = $batchCacheCount;
        $this->getMatchedProductIds = $getMatchedProductIds;
        $this->productTypeDataProvider = $productTypeDataProvider;
        $this->tableSwapper = $tableSwapper ?? ObjectManager::getInstance()->get(TableSwapper::class);
        $this->cache = $cache ?? ObjectManager::getInstance()->get(CacheInterface::class);
        $this->orderProcessingFlag = $orderProcessingFlag
            ?? ObjectManager::getInstance()->get(OrderProcessingFlag::class);
    }

    /**
     * Reindex by ids
     *
     * @param array $ids
     * @return void
     * @throws LocalizedException
     * @api
     */
    public function reindexByProductIds(array $ids)
    {
        if ($this->orderProcessingFlag->isOrderProcessing() && !$this->orderProcessingFlag->isShipmentProcessing()) {
            return;
        }

        try {
            $oldLabelIds = $this->loadCurrentLabelIds($ids);
            $this->doReindexByProductIds($ids);
            $newLabelIds = $this->loadCurrentLabelIds($ids);

            if ($changedProductIds = $this->getChangedProductIds($oldLabelIds, $newLabelIds)) {
                $this->flushCache(Product::CACHE_TAG, $changedProductIds);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new LocalizedException(
                __("Amasty label indexing failed. See details in exception log.")
            );
        }
    }

    /**
     * Reindex by label ids
     *
     * @param array $ids
     * @return void
     * @throws LocalizedException
     * @api
     */
    public function reindexByLabelIds($ids)
    {
        try {
            $this->cleanByLabelIds($ids);
            $this->doReindexByLabelIds($ids);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new LocalizedException(
                __("Amasty label indexing failed. See details in exception log.")
            );
        }
    }

    /**
     * @param $id
     * @throws LocalizedException
     */
    public function reindexByProductId($id)
    {
        $this->reindexByProductIds((array) $id);
    }

    /**
     * @param $id
     * @throws LocalizedException
     */
    public function reindexByLabelId($id)
    {
        $this->reindexByLabelIds([$id]);
    }

    /**
     * @param int[] $productIds
     * @param int[] $labelIds
     */
    private function cleanByProductAndLabelIds(array $productIds, array $labelIds): void
    {
        if (!empty($productIds) && !empty($labelIds)) {
            $connection = $this->resource->getConnection();
            $connection->delete($this->getIndexTable(), [
                $connection->prepareSqlCondition(self::PRODUCT_ID, ['in' => $productIds]),
                $connection->prepareSqlCondition(LabelInterface::LABEL_ID, ['in' => $labelIds])
            ]);
        }
    }

    private function getMainTable(): string
    {
        return $this->resource->getTableName(Uninstall::AMASTY_LABEL_INDEX_TABLE);
    }

    private function getTmpTable(): string
    {
        return $this->tableSwapper->getWorkingTableName(
            $this->resource->getTableName(Uninstall::AMASTY_LABEL_INDEX_TABLE)
        );
    }

    private function getIndexTable(): string
    {
        return $this->isFullIndexRunning ? $this->getTmpTable() : $this->getMainTable();
    }

    /**
     * @param int[] $labelIds
     */
    private function cleanByLabelIds(array $labelIds)
    {
        if (!empty($labelIds)) {
            $connection = $this->resource->getConnection();
            $connection->delete(
                $this->getIndexTable(),
                $connection->prepareSqlCondition(LabelInterface::LABEL_ID, ['in' => $labelIds])
            );
        }
    }

    /**
     * @param array $ids
     * @return $this
     */
    private function doReindexByProductIds($ids)
    {
        $labelsCollection = $this->getFullLabelCollection();
        if ($this->orderProcessingFlag->isOrderProcessing()) {
            $labelsCollection->filterByQtyCondition();
        }
        $labels = $labelsCollection->getItems();

        $this->cleanByProductAndLabelIds($ids, array_keys($labelsCollection->getItems()));

        /** @var Label $label **/
        foreach ($labels as $label) {
            $this->reindexByLabelAndProductIds($label, $ids, false);
        }

        $this->renderChildrenLabelsOnParent($labels, $ids);

        return $this;
    }

    /**
     * @param array $ids
     * @return $this
     */
    private function doReindexByLabelIds($ids)
    {
        $labels = $this->getLabelCollection($ids)->getItems();

        /** @var Label $label **/
        foreach ($labels as $label) {
            $this->reindexByLabelAndProductIds($label);
        }
        $this->flushCache(Label::CACHE_TAG, $ids);

        $this->renderChildrenLabelsOnParent($labels);

        return $this;
    }

    /**
     * @param Label $label
     * @param null $ids
     * @param bool $isNeedFlushCache
     * @return $this
     */
    private function reindexByLabelAndProductIds(Label $label, $ids = null, bool $isNeedFlushCache = true)
    {
        $matchedProductIds = $this->getMatchedProductIds->executeWrapper($label, $ids);
        $this->insertMatchedData($label, $matchedProductIds, $isNeedFlushCache);

        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *
     * @param LabelInterface $label
     * @param array $matchedProductIds
     * @param bool $isNeedFlushCache
     */
    private function insertMatchedData(
        LabelInterface $label,
        array $matchedProductIds,
        bool $isNeedFlushCache = true
    ): void {
        $rows = [];
        $productIds = [];
        $count = 0;

        if (!empty($matchedProductIds)) {
            /** @var int[] $matchedStores **/
            foreach ($matchedProductIds as $productId => $matchedStores) {
                $stores = array_keys($matchedStores);

                foreach ($stores as $storeId) {
                    $rows[] = [
                        self::PRODUCT_ID => (int) $productId,
                        LabelInterface::LABEL_ID => $label->getLabelId(),
                        self::STORE_ID => $storeId
                    ];
                    $count++;
                }

                $productIds[] = (int) $productId;

                if ($count >= $this->batchCount) {
                    $this->insertData($rows);
                    $rows = [];
                    $count = 0;
                }

                if (count($productIds) > $this->batchCacheCount) {
                    if ($isNeedFlushCache) {
                        $this->flushCache(Product::CACHE_TAG, $productIds);
                    }
                    $productIds = [];
                }
            }
        }

        if (!empty($rows)) {
            $this->insertData($rows);
        }

        if ($isNeedFlushCache && !empty($productIds)) {
            $this->flushCache(Product::CACHE_TAG, $productIds);
        }
    }

    private function insertData(array $data): void
    {
        $this->resource->getConnection()->insertOnDuplicate($this->getIndexTable(), $data);
    }

    /**
     * @api
     *
     * Full reindex
     *
     * @return void
     * @throws LocalizedException
     */
    public function reindexFull()
    {
        $this->getTmpTable();
        try {
            $this->isFullIndexRunning = true;
            $this->doReindexFull();
            $this->isFullIndexRunning = false;
            $this->tableSwapper->swapIndexTables([$this->getMainTable()]);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            throw new LocalizedException(__($e->getMessage()), $e);
        }
    }

    /**
     * @param null $labelIds
     * @return mixed
     */
    private function getLabelCollection($labelIds = null): LabelCollection
    {
        $collection = $this->collectionFactory->create();
        $collection->addActiveFilter();

        if ($labelIds) {
            $collection->addFieldToFilter(LabelInterface::LABEL_ID, ['in' => $labelIds]);
        }

        return $collection;
    }

    private function getFullLabelCollection(): LabelCollection
    {
        if ($this->fullLabelCollection === null) {
            $this->fullLabelCollection = $this->collectionFactory->create();
            $this->fullLabelCollection->addActiveFilter();
        }

        return $this->fullLabelCollection;
    }

    /**
     * @return $this
     */
    private function doReindexFull()
    {
        $labels = $this->getFullLabelCollection()->getItems();

        /** @var Label $label **/
        foreach ($labels as $label) {
            $this->reindexByLabelAndProductIds($label);
        }

        $this->renderChildrenLabelsOnParent($labels);

        return $this;
    }

    private function renderChildrenLabelsOnParent(array $labels, ?array $productIds = null): void
    {
        $labelIds = array_reduce($labels, function (array $carry, LabelInterface $label): array {
            if ($label->getUseForParent()) {
                $carry[] = $label->getLabelId();
            }

            return $carry;
        }, []);

        if (!empty($labelIds)) {
            /** @var array $stores * */
            foreach ($this->productTypeDataProvider->getProductChildrenIds($productIds) as $productId => $childrenIds) {
                $this->addChildLabelsToParent($labelIds, $productId, $childrenIds);
            }
        }
    }

    private function addChildLabelsToParent(array $labelIds, int $productId, array $childIds): void
    {
        $connection = $this->resource->getConnection();
        $select = $connection->select();
        $select->from($this->getIndexTable());
        $select->where($connection->prepareSqlCondition(LabelInterface::LABEL_ID, ['in' => $labelIds]));
        $select->where($connection->prepareSqlCondition(self::PRODUCT_ID, ['in' => $childIds]));
        $select->reset(Select::COLUMNS);
        $select->columns([
            LabelInterface::LABEL_ID,
            self::PRODUCT_ID => new \Zend_Db_Expr($productId),
            self::STORE_ID
        ]);
        $query = $connection->insertFromSelect(
            $select,
            $this->getIndexTable(),
            [LabelInterface::LABEL_ID, self::PRODUCT_ID, self::STORE_ID],
            AdapterInterface::INSERT_ON_DUPLICATE
        );
        $connection->query($query);
    }

    private function flushCache(string $cacheTag, array $ids): void
    {
        $this->cacheContext->registerEntities($cacheTag, $ids);
        $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this->cacheContext]);

        $identities = $this->cacheContext->getIdentities();
        if (!empty($identities)) {
            $this->cache->clean($identities);
            $this->cacheContext->flush();
        }
    }

    /**
     * @param int[] $productIds
     * @return array [product_id => label_ids, ...]
     */
    private function loadCurrentLabelIds(array $productIds): array
    {
        $select = $this->resource->getConnection()->select();
        $select->from(
            $this->getMainTable(),
            [
                self::PRODUCT_ID,
                new \Zend_Db_Expr(sprintf('GROUP_CONCAT(%s separator \',\')', LabelInterface::LABEL_ID))
            ]
        )->where(
            sprintf('%s IN (?)', self::PRODUCT_ID),
            $productIds
        )->group(self::PRODUCT_ID);

        return array_unique((array)$this->resource->getConnection()->fetchPairs($select));
    }

    /**
     * @param array $oldLabelIds [productId => '1,2,...', ...]
     * @param array $newLabelIds [productId => '1,2,...', ...]
     * @return int[]
     */
    private function getChangedProductIds(array $oldLabelIds, array $newLabelIds): array
    {
        $changedProductIds = [];

        array_push($changedProductIds, ...array_keys(array_diff_key($oldLabelIds, $newLabelIds)));
        array_push($changedProductIds, ...array_keys(array_diff_key($newLabelIds, $oldLabelIds)));

        foreach ($newLabelIds as $productId => $labelIds) {
            if (!isset($oldLabelIds[$productId])) {
                continue;
            }

            $oldProductLabels = explode(',', $oldLabelIds[$productId]);
            $newProductLabels = explode(',', $labelIds);
            if (array_diff($oldProductLabels, $newProductLabels) || array_diff($newProductLabels, $oldProductLabels)) {
                $changedProductIds[] = $productId;
            }
        }

        return array_unique($changedProductIds);
    }
}
