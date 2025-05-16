<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Plugin\Xnotif\Block\Adminhtml\Analytics\Wanted\ProductList;

use Amasty\Xnotif\Block\Adminhtml\Analytics\Wanted\ProductList;
use Amasty\Xnotif\Model\ResourceModel\Stock\Subscription\Collection as StockCollection;
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
    public function afterGetWantedProducts(ProductList $subject, StockCollection $result): StockCollection
    {
        $this->dateRangeFilterApplier->apply($result, 'add_date');

        return $result;
    }
}
