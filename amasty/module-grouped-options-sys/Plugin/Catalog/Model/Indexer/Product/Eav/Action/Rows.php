<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Grouped Options for Magento 2
 */

namespace Amasty\GroupedOptions\Plugin\Catalog\Model\Indexer\Product\Eav\Action;

use Amasty\GroupedOptions\Model\ResourceModel\GroupAttr\Indexer\Eav\GetGroupedIndexData;
use Amasty\GroupedOptions\Model\ResourceModel\GroupAttr\Indexer\Eav\InsertIndexData;
use Magento\Catalog\Model\Indexer\Product\Eav\Action\Rows as IndexerEavActionRows;

class Rows
{
    /**
     * @var GetGroupedIndexData
     */
    private $getGroupedIndexData;

    /**
     * @var InsertIndexData
     */
    private $insertIndexData;

    public function __construct(
        GetGroupedIndexData $getGroupedIndexData,
        InsertIndexData $insertIndexData
    ) {
        $this->getGroupedIndexData = $getGroupedIndexData;
        $this->insertIndexData = $insertIndexData;
    }

    /**
     * @param IndexerEavActionRows $indexer
     * @param $productIds
     * @return void
     */
    public function afterExecute(IndexerEavActionRows $indexer, $productIds): void
    {
        $groupedIndexData = $this->getGroupedIndexData->execute([
            ['in' => $productIds]
        ]);

        if ($groupedIndexData) {
            $this->insertIndexData->execute($groupedIndexData);
        }
    }
}
