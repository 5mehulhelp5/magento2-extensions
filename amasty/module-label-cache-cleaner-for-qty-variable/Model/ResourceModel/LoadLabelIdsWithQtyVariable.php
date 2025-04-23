<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Label Cache Cleaner for Qty Variable
 */

namespace Amasty\LabelCacheCleanerForQtyVariable\Model\ResourceModel;

use Amasty\Label\Api\Data\LabelFrontendSettingsInterface;
use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Api\Data\LabelTooltipInterface;
use Amasty\Label\Model\Indexer\IndexBuilder;
use Amasty\Label\Model\ResourceModel\Label\Collection;
use Amasty\Label\Model\ResourceModel\Label\CollectionFactory;
use Amasty\Label\Setup\Uninstall;
use Magento\Framework\DB\Select;

class LoadLabelIdsWithQtyVariable
{
    /**
     * @var CollectionFactory
     */
    private $labelsCollectionFactory;

    public function __construct(CollectionFactory $labelsCollectionFactory)
    {
        $this->labelsCollectionFactory = $labelsCollectionFactory;
    }

    /**
     * @param int[] $productIds
     * @param int[] $storeIds
     * @return int[]
     */
    public function execute(array $productIds, array $storeIds): array
    {
        /** @var Collection $labelCollection */
        $labelCollection = $this->labelsCollectionFactory->create();
        $labelCollection->addActiveFilter();

        $select = $labelCollection->getSelect();
        $select->reset(Select::COLUMNS);
        $select->columns([new \Zend_Db_Expr(sprintf('DISTINCT main_table.%s', LabelInterface::LABEL_ID))]);

        $select->joinInner(
            ['ali' => $labelCollection->getResource()->getTable(Uninstall::AMASTY_LABEL_INDEX_TABLE)],
            sprintf(
                'main_table.%1$s = ali.%1$s',
                LabelInterface::LABEL_ID
            ),
            []
        );
        $select->joinInner(
            ['alcp' => $labelCollection->getResource()->getTable(Uninstall::AMASTY_LABEL_CATALOG_PARTS_TABLE)],
            sprintf(
                'main_table.%1$s = alcp.%1$s',
                LabelInterface::LABEL_ID
            ),
            []
        );
        $select->joinInner(
            ['alt' => $labelCollection->getResource()->getTable(Uninstall::AMASTY_LABEL_TOOLTIP_TABLE)],
            sprintf(
                'main_table.%1$s = alt.%1$s',
                LabelInterface::LABEL_ID
            ),
            []
        );

        $select->where(sprintf('%s IN (?)', IndexBuilder::PRODUCT_ID), $productIds);
        $select->where(sprintf('%s IN (?)', IndexBuilder::STORE_ID), $storeIds);
        $orConditions = [];
        $orConditions[] = $labelCollection->getConnection()->prepareSqlCondition(
            LabelFrontendSettingsInterface::LABEL_TEXT,
            ['like' => '%{STOCK}%']
        );
        $orConditions[] = $labelCollection->getConnection()->prepareSqlCondition(
            LabelTooltipInterface::TEXT,
            ['like' => '%{STOCK}%']
        );
        $select->where(implode(' OR ', $orConditions));

        return $labelCollection->getConnection()->fetchCol($select);
    }
}
