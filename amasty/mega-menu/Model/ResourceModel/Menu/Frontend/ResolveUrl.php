<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\ResourceModel\Menu\Frontend;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenu\Model\OptionSource\UrlKey;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\WrapColumns;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class ResolveUrl
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

    public function joinLink(AbstractCollection $collection, ?int $storeId = null): void
    {
        $coalesce = $this->wrapColumns->execute(ItemInterface::TABLE_NAME, [ItemInterface::LINK], $storeId);
        $linkTypeColumn = $this->wrapColumns->execute(
            ItemInterface::TABLE_NAME,
            [ItemInterface::LINK_TYPE],
            $storeId
        )[ItemInterface::LINK_TYPE];
        foreach ($this->urlKeySource->getTablesToJoin() as $type => $table) {
            $collection->getSelect()->joinLeft(
                [$table => $collection->getTable($table)],
                sprintf(
                    '%s.page_id = %s.page_id AND %s = \'%s\'',
                    'main_table',
                    $table,
                    $linkTypeColumn,
                    $type
                ),
                ['identifier']
            );
            array_unshift($coalesce, $table . '.identifier');
        }
        $coalesce[] = '\'\'';

        $collection->getSelect()->columns(sprintf(
            'COALESCE(%s) AS %s',
            implode(', ', $coalesce),
            'url'
        ));
    }
}
