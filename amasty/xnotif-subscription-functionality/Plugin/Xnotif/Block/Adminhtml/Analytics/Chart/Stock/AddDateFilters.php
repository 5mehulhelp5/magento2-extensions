<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Plugin\Xnotif\Block\Adminhtml\Analytics\Chart\Stock;

use Amasty\Xnotif\Block\Adminhtml\Analytics\Chart\Stock;
use Amasty\Xnotif\Model\ResourceModel\Analytics\Request\Stock\Collection;
use Amasty\XnotifSubscriptionFunctionality\Model\Analytics\StockCollection\DateRangeFilterApplier;

class AddDateFilters
{
    /**
     * @var DateRangeFilterApplier
     */
    private $dateRangeFilterApplier;

    public function __construct(
        DateRangeFilterApplier $dateRangeFilterApplier
    ) {
        $this->dateRangeFilterApplier = $dateRangeFilterApplier;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetAnalyticsCollectionData(Stock $subject, Collection $result): Collection
    {
        $this->dateRangeFilterApplier->apply($result, 'date', false);

        return $result;
    }
}
