<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Indexer;

class LabelIndexerRegistry
{
    /**
     * @var int[]
     */
    private $indexedProductIds = [];

    public function registerIndexedProducts(array $productIds): void
    {
        $this->indexedProductIds = array_unique(array_merge($this->indexedProductIds, $productIds));
    }

    public function isCanExecuteList(array $productIds): bool
    {
        $idsForIndexing = array_diff($productIds, $this->indexedProductIds);

        return !empty($idsForIndexing);
    }

    public function isCanExecuteProductId(int $productId): bool
    {
        return !in_array($productId, $this->indexedProductIds);
    }

    public function clearIndexedProductIds(array $productIdsForClean = []): void
    {
        if (empty($productIdsForClean)) {
            $this->indexedProductIds = [];
        }

        $this->indexedProductIds = array_diff($this->indexedProductIds, $productIdsForClean);
    }
}
