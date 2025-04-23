<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Plugin\InventoryIndexer\Indexer\SourceItem\Strategy\Sync;

use Amasty\Label\Model\Indexer\LabelIndexer;
use Amasty\Label\Model\Indexer\LabelIndexerRegistry;
use Amasty\Label\Model\Product\Msi\ConvertSourceItemIds;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\InventoryIndexer\Indexer\SourceItem\Strategy\Sync;

class ReindexProduct
{
    /**
     * @var ConvertSourceItemIds
     */
    private $convertSourceItemIds;

    /**
     * @var LabelIndexerRegistry
     */
    private $labelIndexerRegistry;

    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    public function __construct(
        ConvertSourceItemIds $convertSourceItemIds,
        LabelIndexerRegistry $labelIndexerRegistry,
        IndexerRegistry $indexerRegistry
    ) {
        $this->convertSourceItemIds = $convertSourceItemIds;
        $this->labelIndexerRegistry = $labelIndexerRegistry;
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param Sync $subject
     * @param null $result
     * @param array $sourceItemIds
     */
    public function afterExecuteList(Sync $subject, $result, array $sourceItemIds): void
    {
        $productIdsForReindex = $this->convertSourceItemIds->execute($sourceItemIds);
        $this->labelIndexerRegistry->clearIndexedProductIds($productIdsForReindex);
        $this->indexerRegistry->get(LabelIndexer::INDEXER_ID)->reindexList($productIdsForReindex);
    }
}
