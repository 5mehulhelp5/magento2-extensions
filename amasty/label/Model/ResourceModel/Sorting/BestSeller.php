<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\ResourceModel\Sorting;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class BestSeller extends AbstractDb
{
    public const TABLE_NAME = 'amasty_sorting_bestsellers';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'product_id');
    }

    public function getBestSellerPositionSelect(int $storeId): ?Select
    {
        if ($this->getConnection()->isTableExists($this->getMainTable())) {
            return $this->getConnection()->select()
                ->from(
                    $this->getMainTable(),
                    [
                        'product_id',
                        new \Zend_Db_Expr('@curRow := @curRow + 1 AS bestseller_position')
                    ]
                )
                ->where('store_id = ?', $storeId)
                ->order('qty_ordered DESC')
                ->join(['init' => new \Zend_Db_Expr('(SELECT @curRow := 0)')], '', []);
        }

        return null;
    }
}
