<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Plugin\ProductAlert\Model\Stock;

use Amasty\Xnotif\Model\Product\StockSubscribe;
use Amasty\XnotifSubscriptionFunctionality\Model\Email\CustomerNotification;
use Magento\ProductAlert\Model\Stock;

class StockSubscriptionNotification
{
    /**
     * @var CustomerNotification
     */
    private $customerNotification;

    public function __construct(
        CustomerNotification $customerNotification
    ) {
        $this->customerNotification = $customerNotification;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(Stock $subject, Stock $result): Stock
    {
        $this->customerNotification->sendEmail($result, StockSubscribe::STOCK);

        return $result;
    }
}
