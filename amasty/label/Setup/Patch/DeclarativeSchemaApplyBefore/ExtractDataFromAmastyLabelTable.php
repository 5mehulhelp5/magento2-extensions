<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Setup\Patch\DeclarativeSchemaApplyBefore;

use Amasty\Label\Setup\Uninstall;
use Magento\Framework\Setup\Patch\PatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class ExtractDataFromAmastyLabelTable implements PatchInterface
{
    public const TEMP_TABLE_NAME = 'amasty_label_setup_replica_tmp';

    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    public function __construct(
        SchemaSetupInterface $schemaSetup
    ) {
        $this->schemaSetup = $schemaSetup;
    }

    public static function getDependencies()
    {
        return [
            CheckIsModuleCanProceedUpgradeTo200::class
        ];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply(): ExtractDataFromAmastyLabelTable
    {
        if ($this->isCanApply()) {
            $connection = $this->schemaSetup->getConnection();
            $tmpTableName = $this->schemaSetup->getTable(ExtractDataFromAmastyLabelTable::TEMP_TABLE_NAME);
            $tmpTable = $connection->createTableByDdl(
                $this->getFlatTable(),
                $tmpTableName
            );
            $connection->createTable($tmpTable);
            $select = $connection->select();
            $select->from($this->getFlatTable());
            $connection->query($connection->insertFromSelect($select, $tmpTableName));
        }

        return $this;
    }

    private function isCanApply(): bool
    {
        return $this->schemaSetup->getConnection()->isTableExists(
            $this->getFlatTable()
        );
    }

    private function getFlatTable(): string
    {
        return $this->schemaSetup->getTable(Uninstall::FLAT_LABEL_TABLE);
    }
}
