<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Setup\Patch\DeclarativeSchemaApplyBefore;

use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position;
use Amasty\MegaMenuLite\Setup\Operation\UpdateStore;
use Magento\Framework\Setup\Patch\PatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpdateStorePositionData implements PatchInterface
{
    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    /**
     * @var UpdateStore
     */
    private $updateStore;

    public function __construct(
        UpdateStore $updateStore,
        SchemaSetupInterface $schemaSetup
    ) {
        $this->schemaSetup = $schemaSetup;
        $this->updateStore = $updateStore;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        if ($this->isCanApply()) {
            $this->updateStore->execute($this->schemaSetup);
        }

        return $this;
    }

    private function isCanApply(): bool
    {
        $connection = $this->schemaSetup->getConnection();

        return $connection->isTableExists(Position::TABLE)
            && $connection->tableColumnExists(Position::TABLE, Position::ROOT_CATEGORY_ID);
    }
}
