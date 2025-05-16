<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Setup\Patch\Data;

use Amasty\Xnotif\Api\Analytics\Data\StockInterface as Stock;
use Amasty\Xnotif\Model\Analytics\DefaultDataCollector\DefaultData;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddAnalyticsData implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var DefaultData
     */
    private $defaultData;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        DefaultData $defaultData
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->defaultData = $defaultData;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): self
    {
        $connection = $this->moduleDataSetup->getConnection();
        $stockAnalyticsTable = $this->moduleDataSetup->getTable(Stock::MAIN_TABLE);
        $analyticDataSelect = $connection->select()->from($stockAnalyticsTable)->limit(1);

        if (!$connection->fetchCol($analyticDataSelect)) {
            $this->defaultData->markAsNotCollected();
        }

        return $this;
    }
}
