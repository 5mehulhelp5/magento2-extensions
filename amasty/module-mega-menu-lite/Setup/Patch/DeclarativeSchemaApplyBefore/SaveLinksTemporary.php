<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Setup\Patch\DeclarativeSchemaApplyBefore;

use Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\PatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

class SaveLinksTemporary implements PatchInterface
{
    public const LINK_TEMP_TABLE = 'amasty_menu_link_temp';
    public const ENTITY_ID_COLUMN = 'entity_id';
    public const LINK_TYPE_COLUMN = 'link_type';
    public const LINK_COLUMN = 'link';

    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    public function __construct(SchemaSetupInterface $schemaSetup)
    {
        $this->schemaSetup = $schemaSetup;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    /**
     * @return SaveLinksTemporary
     */
    public function apply()
    {
        if ($this->canApply()) {
            $connection = $this->schemaSetup->getConnection();

            $this->createTempTable();

            $select = $connection->select()->from(
                ['main_table' => $this->schemaSetup->getTable(LinkInterface::TABLE_NAME)],
                [LinkInterface::ENTITY_ID, 'link_type', 'link']
            );
            $sql = $connection->insertFromSelect($select, $this->schemaSetup->getTable(self::LINK_TEMP_TABLE));
            $connection->query($sql);
        }

        return $this;
    }

    private function canApply(): bool
    {
        try {
            return $this->schemaSetup->getConnection()->tableColumnExists(
                $this->schemaSetup->getTable(LinkInterface::TABLE_NAME),
                'link_type'
            );
        } catch (Zend_Db_Exception $e) {
            return false;
        }
    }

    private function createTempTable(): void
    {
        $connection = $this->schemaSetup->getConnection();

        $linkTempTable = $connection->newTable($this->schemaSetup->getTable(self::LINK_TEMP_TABLE));
        $linkTempTable->addColumn(
            self::ENTITY_ID_COLUMN,
            Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => false,
                'nullable' => false,
                'primary' => true
            ]
        );
        $linkTempTable->addColumn(
            self::LINK_TYPE_COLUMN,
            Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => false,
                'nullable' => true
            ]
        );
        $linkTempTable->addColumn(
            self::LINK_COLUMN,
            Table::TYPE_TEXT,
            255,
            [
                'nullable' => true
            ]
        );

        $connection->createTemporaryTable($linkTempTable);
    }
}
