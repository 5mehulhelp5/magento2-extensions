<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\ResourceModel\Indexer;

use Amasty\Label\Model\Indexer\IndexBuilder;
use Amasty\Label\Setup\Uninstall;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;

class IndexedProductDataProvider
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    public function getIndexedProductIds(int $labelId): array
    {
        $indexTable = $this->resourceConnection->getTableName(Uninstall::AMASTY_LABEL_INDEX_TABLE);
        $connection = $this->resourceConnection->getConnection();

        return $connection->fetchCol(
            $connection->select()
                ->from($indexTable)
                ->where('label_id = ?', $labelId)
                ->reset(Select::COLUMNS)
                ->columns(IndexBuilder::PRODUCT_ID)
        );
    }
}
