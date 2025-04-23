<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position;

use Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface;
use Magento\Framework\App\ResourceConnection;

class UpdateChildPosition
{
    private const WILDCARD   = '%';

    /**
     * @var ResourceConnection
     */
    private $resource;

    public function __construct(
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    public function execute(LinkInterface $object): void
    {
        $oldPath = $object->getOrigData(LinkInterface::PATH) . $object->getEntityId() . LinkInterface::PATH_SEPARATOR;
        $newPath = $object->getPath() . $object->getEntityId() . LinkInterface::PATH_SEPARATOR;

        $this->updatePath($oldPath, $newPath);
        $this->updateLevel($newPath);
    }

    private function updatePath(string $oldPath, string $newPath): void
    {
        $replaceCondition =  sprintf('replace(%s, "%s", "%s")', LinkInterface::PATH, $oldPath, $newPath);
        $pathCondition = sprintf('%s like ?', LinkInterface::PATH);
        $this->resource->getConnection()->update(
            [$this->resource->getTableName(LinkInterface::TABLE_NAME)],
            [LinkInterface::PATH => new \Zend_Db_Expr($replaceCondition)],
            [$pathCondition => $oldPath . self::WILDCARD]
        );
    }

    private function updateLevel(string $path): void
    {
        $replaceCondition = sprintf('(LENGTH(%1$s) - LENGTH(REPLACE(%1$s,"%2$s","")) - 1)', LinkInterface::PATH, '/');
        $pathCondition = sprintf('%s like ?', LinkInterface::PATH);
        $this->resource->getConnection()->update(
            [$this->resource->getTableName(LinkInterface::TABLE_NAME)],
            [LinkInterface::LEVEL => new \Zend_Db_Expr($replaceCondition)],
            [$pathCondition => $path . self::WILDCARD]
        );
    }
}
