<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Setup\Patch\Data;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class ApplyWhoCanBuy implements DataPatchInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
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
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('core_config_data');

        $select = $connection->select()
            ->from($tableName)
            ->where('path = ?', 'amasty_advancedreview/general/allow_guest');

        $settings = $connection->fetchAll($select);

        foreach ($settings as $row) {
            $connection->insertOnDuplicate(
                $tableName,
                [
                    'scope' => $row['scope'],
                    'scope_id' => $row['scope_id'],
                    'path' => 'amasty_advancedreview/general/who_can_submit',
                    'value' => $row['value'] ? 'all' : 'registered'
                ],
                ['value']
            );
        }

        return $this;
    }
}
