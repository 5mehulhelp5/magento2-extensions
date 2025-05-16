<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Analytics\DefaultDataCollector;

use Magento\Framework\FlagManager;

class DefaultData
{
    private const FLAG_CODE_VALUE = 'amasty_xnotif_analytics_init';

    /**
     * @var FlagManager
     */
    private $flagManager;

    /**
     * @var bool
     */
    private $collectedFlag;

    public function __construct(FlagManager $flagManager)
    {
        $this->flagManager = $flagManager;
    }

    public function isCollected(): bool
    {
        if ($this->collectedFlag === null) {
            $this->collectedFlag = !$this->flagManager->getFlagData(self::FLAG_CODE_VALUE);
        }
        return $this->collectedFlag;
    }

    public function markAsNotCollected(): void
    {
        $this->flagManager->saveFlag(self::FLAG_CODE_VALUE, 1);
    }

    public function markAsCollected(): void
    {
        $this->flagManager->deleteFlag(self::FLAG_CODE_VALUE);
    }
}
