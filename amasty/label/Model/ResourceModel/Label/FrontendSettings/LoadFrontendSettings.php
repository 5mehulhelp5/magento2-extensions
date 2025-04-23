<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\ResourceModel\Label\FrontendSettings;

use Amasty\Label\Api\Data\LabelFrontendSettingsInterface;
use Amasty\Label\Setup\Uninstall;
use Magento\Framework\App\ResourceConnection;

class LoadFrontendSettings
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    public function execute(array $labelIds, ?int $mode = null): array
    {
        $select = $this->resourceConnection->getConnection()->select();
        $select->from(
            $this->resourceConnection->getTableName(Uninstall::AMASTY_LABEL_CATALOG_PARTS_TABLE)
        )->where(
            LabelFrontendSettingsInterface::LABEL_ID . ' IN (?)',
            $labelIds
        );

        if ($mode !== null) {
            $select->where(LabelFrontendSettingsInterface::TYPE . ' = ?', $mode);
        }

        return $this->resourceConnection->getConnection()->fetchAll($select);
    }
}
