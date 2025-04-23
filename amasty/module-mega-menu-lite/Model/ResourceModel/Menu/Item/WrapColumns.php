<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item;

use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\Store;

class WrapColumns
{
    /**
     * @var ResourceConnection
     */
    private $connection;

    public function __construct(ResourceConnection $connection)
    {
        $this->connection = $connection;
    }

    public function execute(
        string $tableAlias,
        ?array $fields,
        ?int $storeId = Store::DEFAULT_STORE_ID,
        string $defaultTableAlias = null
    ): ?array {
        if ($fields === null) {
            return null;
        }

        if ($fields && $storeId !== Store::DEFAULT_STORE_ID) {
            $columns = [];
            if (!$defaultTableAlias) {
                $defaultTableAlias = sprintf('%s_%s', $tableAlias, Store::DEFAULT_STORE_ID);
            }
            $storeTableAlias = sprintf('%s_%s', $tableAlias, $storeId);

            foreach ($fields as $field) {
                $columns[$field] = $this->connection->getConnection()->getIfNullSql(
                    sprintf('%s.%s', $storeTableAlias, $field),
                    sprintf('%s.%s', $defaultTableAlias, $field)
                );
            }
        }

        return $columns ?? array_combine($fields, $fields);
    }
}
