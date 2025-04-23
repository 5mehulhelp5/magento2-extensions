<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\ResourceModel\Menu;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenu\Model\OptionSource\UrlKey;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\WrapColumns;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class ResourceResolver
{
    /**
     * @var UrlKey
     */
    private $urlKeySource;

    /**
     * @var WrapColumns
     */
    private $wrapColumns;

    public function __construct(
        UrlKey $urlKeySource,
        WrapColumns $wrapColumns
    ) {
        $this->urlKeySource = $urlKeySource;
        $this->wrapColumns = $wrapColumns;
    }

    /**
     * Deprecated because now links stored in store table.
     * @deprecated
     * @see ResourceResolver::joinLinkByStore
     */
    // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    public function joinLink(AbstractCollection $collection, string $tableAlias, string $columnAlias): void
    {
    }

    public function joinLinkByStore(
        AbstractCollection $collection,
        string $linkTableAlias,
        string $columnAlias,
        string $itemContentAlias,
        int $storeId
    ): void {
        $coalesce[] = $this->wrapColumns->execute(
            $itemContentAlias,
            [ItemInterface::LINK],
            $storeId
        )[ItemInterface::LINK];
        $linkTypeColumn = $this->wrapColumns->execute(
            $itemContentAlias,
            [ItemInterface::LINK_TYPE],
            $storeId
        )[ItemInterface::LINK_TYPE];
        foreach ($this->urlKeySource->getTablesToJoin() as $type => $table) {
            $collection->getSelect()->joinLeft(
                [$table => $collection->getTable($table)],
                sprintf(
                    '%s.page_id = %s.page_id AND %s = \'%s\'',
                    $linkTableAlias,
                    $table,
                    $linkTypeColumn,
                    $type
                ),
                ['identifier']
            );
            $coalesce[] = $table . '.identifier';
        }
        $coalesce[] = '\'\'';

        $collection->getSelect()->columns(sprintf(
            'COALESCE(%s) AS %s',
            implode(', ', $coalesce),
            $columnAlias
        ));
    }
}
