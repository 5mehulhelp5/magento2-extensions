<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Reports Base for Magento 2
 */

namespace Amasty\Reports\Model\ResourceModel\Customers\Returning\Collection;

use Amasty\Reports\Model\ConfigProvider;
use Amasty\Reports\Model\ResourceModel\Filters\AddInterval;
use Amasty\Reports\Model\Utilities\TimeZoneExpressionModifier;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;

class SelectProvider
{
    /**
     * @var ResourceConnection
     */
    private $connection;

    /**
     * @var TimeZoneExpressionModifier
     */
    private $timeZoneExpressionModifier;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var AddInterval
     */
    private $addInterval;

    public function __construct(
        ResourceConnection $connection,
        TimeZoneExpressionModifier $timeZoneExpressionModifier,
        ConfigProvider $configProvider = null, // TODO move to not optional
        AddInterval $addInterval = null
    ) {
        $this->connection = $connection;
        $this->timeZoneExpressionModifier = $timeZoneExpressionModifier;
        $this->configProvider = $configProvider
            ?? ObjectManager::getInstance()->get(ConfigProvider::class);
        $this->addInterval = $addInterval
            ?? ObjectManager::getInstance()->get(AddInterval::class);
    }

    public function getNewCustomersQuery(): \Zend_Db_Expr
    {
        $orderTable = $this->connection->getTableName('sales_order');
        $orderStatusWhereClause = $this->getOrderStatusWhereClause();
        $intervalWhereClause = $this->addInterval->getIntervalWhereClause();

        return new \Zend_Db_Expr(
            '( SELECT COUNT( so1.increment_id )
                FROM ' . $orderTable . ' so1
                INNER JOIN (
                    SELECT customer_email, MIN(created_at) AS created_at
                    FROM ' . $orderTable . '
                    GROUP BY customer_email
                ) fo
                ON so1.customer_email = fo.customer_email
                AND so1.created_at = fo.created_at
                WHERE ' . $intervalWhereClause . $orderStatusWhereClause .
            ' ) '
        );
    }

    public function getReturningCustomersSelect(): \Zend_Db_Expr
    {
        $orderTable = $this->connection->getTableName('sales_order');
        $orderStatusWhereClause = $this->getOrderStatusWhereClause();
        $intervalWhereClause = $this->addInterval->getIntervalWhereClause();

        return new \Zend_Db_Expr(
            '( SELECT COUNT( so1.increment_id )
                FROM ' . $orderTable . ' so1
                INNER JOIN (
                    SELECT customer_email, MIN(created_at) AS created_at
                    FROM ' . $orderTable . '
                    GROUP BY customer_email
                ) fo
                ON so1.customer_email = fo.customer_email
                AND so1.created_at > fo.created_at
                WHERE ' . $intervalWhereClause . $orderStatusWhereClause .
            ' ) '
        );
    }

    public function getPercentSelect(): \Zend_Db_Expr
    {
        return new \Zend_Db_Expr(
            '(ROUND((' . $this->getReturningCustomersSelect() . ') / ('
            . $this->getReturningCustomersSelect() . ' + ' . $this->getNewCustomersQuery() . ') * 100))'
        );
    }

    private function getOrderStatusWhereClause(): string
    {
        $select = $this->connection->getConnection()->select();
        $statuses = $this->configProvider->getOrderStatuses();

        return empty($statuses) ? ''
            : ' AND so1.status IN (' . implode(',', array_map(function ($status) use ($select) {
                return $select->getAdapter()->quote($status);
            }, $statuses)) . ') ';
    }
}
