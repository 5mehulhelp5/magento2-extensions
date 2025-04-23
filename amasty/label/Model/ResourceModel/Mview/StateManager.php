<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\ResourceModel\Mview;

use Exception;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;

class StateManager
{
    /**
     * @var ResourceConnection
     */
    private $connection;

    public function __construct(
        ResourceConnection $connection
    ) {
        $this->connection = $connection;
    }

    public function get(string $indexer): array
    {
        $mviewTable = $this->connection->getTableName('mview_state');
        $connection = $this->connection->getConnection();

        return $connection->fetchCol(
            $connection->select()
                ->from($mviewTable)
                ->where('view_id = ?', $indexer)
                ->reset(Select::COLUMNS)
                ->columns('version_id')
                ->limit(1)
        );
    }

    public function isScheduled(string $indexer, string $tableName, int $entityId): bool
    {
        try {
            $clTable = $this->connection->getTableName($tableName);
            $connection = $this->connection->getConnection();
            $version = $this->get($indexer);
            if (empty($version)) {
                return false;
            }

            return (bool)$connection->fetchOne(
                $connection->select()
                    ->from($clTable)
                    ->where('version_id > ?', $version)
                    ->where('entity_id = ?', $entityId)
                    ->limit(1)
                    ->columns(['version_id'])
            );
        } catch (Exception $e) {
            return false;
        }
    }
}
