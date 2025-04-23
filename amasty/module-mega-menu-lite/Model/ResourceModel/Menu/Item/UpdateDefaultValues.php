<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Sql\ColumnValueExpressionFactory;

class UpdateDefaultValues
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var ColumnValueExpressionFactory
     */
    private $expressionFactory;

    public function __construct(
        ResourceConnection $resourceConnection,
        ColumnValueExpressionFactory $expressionFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->expressionFactory = $expressionFactory;
    }

    public function execute(array $items): void
    {
        $replaceCondition =  sprintf('replace(%s, "%s, ", "")', ItemInterface::USE_DEFAULT, ItemInterface::SORT_ORDER);
        $idCondition = sprintf('%s IN (?)', ItemInterface::ENTITY_ID);
        $storeCondition = sprintf('%s IN (?)', ItemInterface::STORE_ID);
        $this->resourceConnection->getConnection()->update(
            [$this->resourceConnection->getTableName(ItemInterface::TABLE_NAME)],
            [ItemInterface::USE_DEFAULT => $this->expressionFactory->create(['expression' => $replaceCondition])],
            [
                $idCondition => array_column($items, ItemInterface::ENTITY_ID),
                $storeCondition => array_column($items, Position::STORE_VIEW)
            ]
        );
    }
}
