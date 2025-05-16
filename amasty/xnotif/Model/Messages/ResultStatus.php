<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Messages;

class ResultStatus
{
    public const SUBSCRIPTION_ADDED_STATUS = 1;
    public const SUBSCRIPTION_ALREADY_EXIST_STATUS = 2;

    public const MESSAGES = [
        self::SUBSCRIPTION_ADDED_STATUS => 'Alert subscription has been saved.',
        self::SUBSCRIPTION_ALREADY_EXIST_STATUS => 'Thank you! You are already subscribed to this product.'
    ];
}
