<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Plugins\Cron\Model\ConfigInterface;

use Amasty\Xnotif\Model\Analytics\DefaultDataCollector\DefaultData;
use Magento\Cron\Model\ConfigInterface;

class DisableAnalyticsJob
{
    /**
     * @var DefaultData
     */
    private $defaultData;

    public function __construct(DefaultData $defaultData)
    {
        $this->defaultData = $defaultData;
    }

    /**
     * @see ConfigInterface::getJobs
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetJobs(ConfigInterface $subject, array $jobs): array
    {
        if ($this->defaultData->isCollected() && isset($jobs['amasty_xnotif']['amasty_xnotif_analytics_init'])) {
            unset($jobs['amasty_xnotif']['amasty_xnotif_analytics_init']);
        }
        return $jobs;
    }
}
