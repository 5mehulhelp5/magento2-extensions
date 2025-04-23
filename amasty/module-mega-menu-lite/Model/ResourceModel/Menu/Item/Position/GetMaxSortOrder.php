<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position;

use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position;
use Magento\Framework\App\ResourceConnection;

class GetMaxSortOrder
{
    private const DEFAULT_SORT_ORDER = 0;

    /**
     * @var array
     */
    private $maxSortOrder = [];

    /**
     * @var ResourceConnection
     */
    private $resource;

    public function __construct(
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    public function execute(int $storeId): int
    {
        if (!isset($this->maxSortOrder[$storeId])) {
            $select = $this->resource->getConnection()->select()
                ->from(
                    $this->resource->getTableName(Position::TABLE),
                    'MAX(sort_order)'
                )
                ->where(Position::STORE_VIEW . ' = ?', $storeId);

            $this->maxSortOrder[$storeId] = $this->resource->getConnection()->fetchOne($select);
        }

        return $this->maxSortOrder[$storeId] === null
            ? self::DEFAULT_SORT_ORDER : (int)++$this->maxSortOrder[$storeId];
    }
}
