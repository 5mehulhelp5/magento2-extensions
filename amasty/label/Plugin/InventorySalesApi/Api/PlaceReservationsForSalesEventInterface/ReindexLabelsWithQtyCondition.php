<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Plugin\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface;

use Amasty\Label\Model\Indexer\LabelIndexer;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\InventorySalesApi\Api\Data\ItemToSellInterface;
use Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface;

class ReindexLabelsWithQtyCondition
{
    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    public function __construct(ProductResource $productResource, IndexerRegistry $indexerRegistry)
    {
        $this->productResource = $productResource;
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * @param PlaceReservationsForSalesEventInterface $subject
     * @param null $result
     * @param ItemToSellInterface[] $items
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(PlaceReservationsForSalesEventInterface $subject, $result, array $items): void
    {
        if (empty($items)) {
            return;
        }

        $productSkuList = array_map(function (ItemToSellInterface $itemToSell) {
            return $itemToSell->getSku();
        }, $items);

        $this->indexerRegistry->get(LabelIndexer::INDEXER_ID)->reindexList(
            $this->productResource->getProductsIdsBySkus($productSkuList)
        );
    }
}
