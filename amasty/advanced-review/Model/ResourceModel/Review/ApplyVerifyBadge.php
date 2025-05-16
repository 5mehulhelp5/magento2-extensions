<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\ResourceModel\Review;

use Amasty\AdvancedReview\Api\Data\CommentInterface;
use Magento\Framework\Config\ConfigOptionsListConstants as Constants;
use Magento\Framework\DB\Select;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Model\Order\Item;
use Magento\Framework\DB\Adapter\AdapterInterface;

class ApplyVerifyBadge extends \Magento\Review\Model\ResourceModel\Review\Collection
{
    /**
     * Method for update verified_buyer column of the review table
     *
     * Does not work for grouped products
     */
    public function execute(): void
    {
        $connection = $this->getConnection();
        $select = $this->getSelect()
            ->reset(Select::COLUMNS)
            ->columns(['main_table.review_id'])
            ->join(
                ['sales_order_item' => $this->getTable('sales_order_item')],
                'main_table.entity_pk_value = sales_order_item.product_id',
                []
            )->join(
                ['sales_order' => $this->getTable('sales_order')],
                'sales_order.entity_id = sales_order_item.order_id'
                . ' AND detail.customer_id=sales_order.customer_id AND sales_order.created_at < main_table.created_at',
                []
            )->group('main_table.review_id');

        $data = $connection->fetchAll($select);
        if (!empty($data)) {
            foreach ($data as &$item) {
                $item['verified_buyer'] = 1;
            }

            $connection->insertOnDuplicate(
                $this->getMainTable(),
                $data,
                ['review_id', 'verified_buyer']
            );
        }
    }

    /**
     * Check is applicable execution
     *
     * @return bool
     */
    public function isApplicable(): bool
    {
        $connection = $this->getConnection();
        $select = $this->getSelect();
        $select->reset(Select::COLUMNS)
            ->columns(['main_table.review_id'])
            ->where('verified_buyer = ?', 1)
            ->limit(1);

        $entityId = $connection->fetchOne($select);

        return !empty($entityId);
    }
}
