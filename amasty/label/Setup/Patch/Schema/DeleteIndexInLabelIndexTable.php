<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Setup\Patch\Schema;

use Amasty\Label\Setup\Uninstall;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class DeleteIndexInLabelIndexTable implements SchemaPatchInterface
{
    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    public function __construct(
        SchemaSetupInterface $schemaSetup
    ) {
        $this->schemaSetup = $schemaSetup;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): DeleteIndexInLabelIndexTable
    {
        $connection = $this->schemaSetup->getConnection();
        $indexTable = $this->schemaSetup->getTable(Uninstall::AMASTY_LABEL_INDEX_TABLE);
        if ($connection->isTableExists($indexTable) && ($indexName = $this->getIndexName())) {
            $connection->dropIndex(
                $indexTable,
                $indexName
            );
        }

        return $this;
    }

    private function getIndexName(): string
    {
        $connection = $this->schemaSetup->getConnection();
        $indexTable = $this->schemaSetup->getTable(Uninstall::AMASTY_LABEL_INDEX_TABLE);
        $partialIndexName = $connection->getIndexName($indexTable, ['label_id']);
        $indexList = $connection->getIndexList($indexTable);
        $indexes = array_filter(array_keys($indexList), function ($key) use ($partialIndexName) {
            return stripos($key, $partialIndexName) !== false;
        });

        return reset($indexes) ?: '';
    }
}
