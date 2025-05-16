<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Plugin\Xnotif\Block\Adminhtml\Analytics\Request\Stock\Collection;

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

    public function beforeGetTotalRow(Collection $subject): void
    {
        $this->dateRangeFilterApplier->apply($subject, 'date', false);
    }
}
