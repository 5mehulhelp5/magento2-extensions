<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Setup\Patch\Data;

use Amasty\Faq\Model\ResourceModel\VisitStat as VisitStatResource;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\NonTransactionableInterface;

class ClearViewStatTable implements DataPatchInterface, NonTransactionableInterface
{
    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    public function __construct(
        ResourceInterface $moduleResource,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleResource = $moduleResource;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public function apply()
    {
        $setupDataVersion = (string)$this->moduleResource->getDataVersion('Amasty_Faq');
        if ($setupDataVersion && version_compare($setupDataVersion, '2.4.0', '<')) {
            $this->moduleDataSetup->getConnection()->truncateTable(
                $this->moduleDataSetup->getTable(VisitStatResource::TABLE_NAME)
            );
        }
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
