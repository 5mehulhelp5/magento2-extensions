<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\ResourceModel\Analytics\Request\Stock;

use Amasty\Xnotif\Api\Analytics\Data\StockInterface;
use Amasty\Xnotif\Model\Analytics\Request\Stock;
use Amasty\Xnotif\Model\ResourceModel\Analytics\Request\Stock as StockResource;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public const MONTH_LIMIT = 12;

    protected function _construct()
    {
        $this->_init(Stock::class, StockResource::class);
    }

    /**
     * @return $this
     */
    public function groupByMonth()
    {
        $this->prepareSum();
        $this->getSelect()
            ->group(new \Zend_Db_Expr(sprintf('MONTH(%s)', StockInterface::DATE)))
            ->group(new \Zend_Db_Expr(sprintf('YEAR(%s)', StockInterface::DATE)))
            ->columns(StockInterface::DATE)
            ->order(sprintf('%s %s', StockInterface::DATE, Select::SQL_DESC))
            ->limit(self::MONTH_LIMIT);

        return $this;
    }

    private function prepareSum()
    {
        $this->getSelect()
            ->reset(Select::COLUMNS)
            ->columns(
                array_map(
                    function ($field) {
                        return 'SUM(`' . $field . '`) as ' . $field;
                    },
                    [StockInterface::SUBSCRIBED, StockInterface::SENT, StockInterface::ORDERS]
                )
            );
    }

    /**
     * @return array
     */
    public function getTotalRow()
    {
        $this->prepareSum();

        return $this->getConnection()->fetchRow($this->getSelect());
    }
}
