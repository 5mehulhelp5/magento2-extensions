<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Setup\Patch\Data;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Setup\Patch\DeclarativeSchemaApplyBefore\SaveLinksTemporary;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Store\Model\Store;
use Zend_Db_Exception;

class SaveLinksByStore implements DataPatchInterface
{
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
     * @return SaveLinksByStore
     */
    public function apply()
    {
        if ($this->canApply()) {
            $this->updateDefaultStoreValues();
            $this->updateCustomStoreValues();
        }

        return $this;
    }

    private function canApply(): bool
    {
        try {
            return $this->schemaSetup->getConnection()->tableColumnExists(
                $this->schemaSetup->getTable(SaveLinksTemporary::LINK_TEMP_TABLE),
                SaveLinksTemporary::ENTITY_ID_COLUMN
            );
        } catch (Zend_Db_Exception $e) {
            return false;
        }
    }

    private function updateDefaultStoreValues(): void
    {
        $connection = $this->schemaSetup->getConnection();

        $select = $connection->select()->from(
            false,
            []
        )->join(
            ['temp' => $this->schemaSetup->getTable(SaveLinksTemporary::LINK_TEMP_TABLE)],
            sprintf(
                'temp.%s = store_table.%s AND store_table.%s = "%s" AND store_table.%s = %d',
                SaveLinksTemporary::ENTITY_ID_COLUMN,
                ItemInterface::ENTITY_ID,
                ItemInterface::TYPE,
                ItemInterface::CUSTOM_TYPE,
                ItemInterface::STORE_ID,
                Store::DEFAULT_STORE_ID
            ),
            [SaveLinksTemporary::LINK_TYPE_COLUMN, SaveLinksTemporary::LINK_COLUMN]
        );

        $sql = $connection->updateFromSelect($select, [
            'store_table' => $this->schemaSetup->getTable(ItemInterface::TABLE_NAME)
        ]);

        $connection->query($sql);
    }

    private function updateCustomStoreValues(): void
    {
        $connection = $this->schemaSetup->getConnection();

        $select = $connection->select()->from(
            false,
            [ItemInterface::USE_DEFAULT => $connection->getConcatSql([
                ItemInterface::USE_DEFAULT,
                sprintf('"%s"', ItemInterface::LINK),
                sprintf('"%s"', ItemInterface::LINK_TYPE)
            ], ItemInterface::SEPARATOR)]
        )->join(
            ['temp' => $this->schemaSetup->getTable(SaveLinksTemporary::LINK_TEMP_TABLE)],
            sprintf(
                'temp.%s = store_table.%s AND store_table.%s = "%s" AND store_table.%s != %d',
                SaveLinksTemporary::ENTITY_ID_COLUMN,
                ItemInterface::ENTITY_ID,
                ItemInterface::TYPE,
                ItemInterface::CUSTOM_TYPE,
                ItemInterface::STORE_ID,
                Store::DEFAULT_STORE_ID
            ),
            []
        );

        $sql = $connection->updateFromSelect($select, [
            'store_table' => $this->schemaSetup->getTable(ItemInterface::TABLE_NAME)
        ]);

        $connection->query($sql);
    }
}
