<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Plugins\ProductAlert\Model\Observer;

use Magento\ProductAlert\Model\Observer;

class DisableCronJob
{
    /**
     * Disable product alert cron job
     * @see Observer::process()
     *
     * @return void
     */
    public function aroundProcess(): void
    {
    }
}
