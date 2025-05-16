<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Observer;

use Amasty\XnotifSubscriptionFunctionality\Model\Email\CustomerNotification;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\ProductAlert\Model\Price;

/**
 * amasty_xnotif_after_save_price_model
 */
class PriceSubscriptionNotification implements ObserverInterface
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

    public function execute(Observer $observer): void
    {
        /** @var Price $model */
        $model = $observer->getModel();
        $this->customerNotification->sendEmail($model, $observer->getModelType());
    }
}
