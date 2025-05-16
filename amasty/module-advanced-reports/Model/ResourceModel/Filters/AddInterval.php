<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Reports Base for Magento 2
 */

namespace Amasty\Reports\Model\ResourceModel\Filters;

use Amasty\Reports\Model\Utilities\TimeZoneExpressionModifier;
use Magento\Framework\Data\Collection\AbstractDb;

class AddInterval
{
    /**
     * @var RequestFiltersProvider
     */
    private $filtersProvider;

    /**
     * @var TimeZoneExpressionModifier
     */
    private $timeZoneExpressionModifier;

    public function __construct(
        RequestFiltersProvider $filtersProvider,
        TimeZoneExpressionModifier $timeZoneExpressionModifier
    ) {
        $this->filtersProvider = $filtersProvider;
        $this->timeZoneExpressionModifier = $timeZoneExpressionModifier;
    }

    public function execute(
        AbstractDb $collection,
        string $dateFiled = 'created_at',
        string $tablePrefix = 'main_table'
    ): void {

        $interval = $this->getIntervalType();
        $collection->setFlag('interval', $interval);

        $fieldExpression = $this->timeZoneExpressionModifier->execute($dateFiled);
        $completeFieldExpression = $this->timeZoneExpressionModifier->execute(
            sprintf("%s.%s", $tablePrefix, $dateFiled)
        );

        switch ($interval) {
            case 'year':
                $collection->getSelect()
                    ->columns([
                        'period' => "YEAR($completeFieldExpression)",
                    ])
                    ->group("YEAR($completeFieldExpression)");
                break;
            case 'month':
                $collection->getSelect()
                    ->columns([
                        'period' => "DATE_FORMAT($completeFieldExpression,'%Y-%m')",
                    ])
                    ->group("DATE_FORMAT($completeFieldExpression,'%Y-%m')");
                break;
            case 'week':
                $collection->getSelect()
                    ->columns([
                        'period' => "CONCAT(ADDDATE(DATE($completeFieldExpression), "
                            . "INTERVAL 1-DAYOFWEEK($completeFieldExpression) DAY), "
                            . "' - ', ADDDATE(DATE($completeFieldExpression), "
                            . "INTERVAL 7-DAYOFWEEK($completeFieldExpression) DAY))",
                    ])
                    ->group("WEEK($completeFieldExpression)");
                break;
            case 'day':
            default:
                $collection->getSelect()
                    ->columns([
                        'period' => "DATE($completeFieldExpression)",
                    ])
                    ->group("DATE($completeFieldExpression)");
        }
    }

    private function getIntervalType(): string
    {
        $filters = $this->filtersProvider->execute();

        return $filters['interval'] ?? 'day';
    }

    public function getIntervalWhereClause(): string
    {
        $interval = $this->getIntervalType();

        switch ($interval) {
            case 'year':
                return "YEAR(so1.created_at) = YEAR(created_date) ";
            case 'month':
                return "DATE_FORMAT(so1.created_at,'%Y-%m') = DATE_FORMAT(created_date,'%Y-%m') ";
            case 'week':
                return "(CONCAT(ADDDATE(DATE(so1.created_at), "
                    . "INTERVAL 1-DAYOFWEEK(so1.created_at) DAY), "
                    . "' - ', ADDDATE(DATE(so1.created_at), "
                    . "INTERVAL 7-DAYOFWEEK(so1.created_at) DAY))) = "
                    . "(CONCAT(ADDDATE(DATE(created_date), "
                    . "INTERVAL 1-DAYOFWEEK(created_date) DAY), "
                    . "' - ', ADDDATE(DATE(created_date), "
                    . "INTERVAL 7-DAYOFWEEK(created_date) DAY))) ";
            case 'day':
            default:
                return "DATE(so1.created_at) = created_date ";
        }
    }
}
