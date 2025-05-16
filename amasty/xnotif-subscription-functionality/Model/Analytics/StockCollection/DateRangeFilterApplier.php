<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Model\Analytics\StockCollection;

use Amasty\XnotifSubscriptionFunctionality\Model\Analytics\DataFilterProvider;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class DateRangeFilterApplier
{
    /**
     * @var DataFilterProvider
     */
    private $dataFilterProvider;

    public function __construct(
        DataFilterProvider $dataFilterProvider
    ) {
        $this->dataFilterProvider = $dataFilterProvider;
    }
    public function apply(AbstractCollection $collection, string $dateField, bool $resetLimit = true): void
    {
        $fromDate = $this->dataFilterProvider->getFromDate();
        $toDate = $this->dataFilterProvider->getToDate();
        $filters = $this->prepareDateFilter($fromDate, $toDate, $collection->getConnection(), $dateField);

        if (count($filters) && $resetLimit) {
            $collection->getSelect()
                ->reset(Select::LIMIT_COUNT)
                ->reset(Select::LIMIT_OFFSET);
        }

        foreach ($filters as $filter) {
            $collection->getSelect()->where($filter);
        }
    }

    private function prepareDateFilter(
        ?string $fromDate,
        ?string $toDate,
        AdapterInterface $connection,
        string $dateField
    ): array {
        $filters = [];

        if ($fromDate) {
            $filters[] = $connection->quoteInto($dateField . ' >= ?', $fromDate);
        }

        if ($toDate) {
            $filters[] = $connection->quoteInto($dateField . ' <= ?', $toDate);
        }

        return $filters;
    }
}
