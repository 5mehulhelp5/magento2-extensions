<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Backend\Builder;

use Amasty\MegaMenuLite\Model\OptionSource\UrlKey;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position\Collection as PositionCollection;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position\CollectionFactory as PositionCollectionFactory;
use Magento\Framework\Data\Collection\AbstractDb;

class GetItemCollection
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var PositionCollectionFactory
     */
    private $positionCollectionFactory;

    public function __construct(
        PositionCollectionFactory $positionCollectionFactory,
        Registry $registry,
        UrlKey $urlKey // @deprecated
    ) {
        $this->registry = $registry;
        $this->positionCollectionFactory = $positionCollectionFactory;
    }

    public function execute(): PositionCollection
    {
        $storeId = $this->registry->getStoreId();
        $itemCollection = $this->positionCollectionFactory->create()->getSortedCollection($storeId);
        $itemCollection->joinPositionTableByStore($storeId);
        $itemCollection->joinLinkTable();
        $itemCollection->addDefaultLevelFilter();
        $itemCollection->setOrder(Position::POSITION, AbstractDb::SORT_ORDER_ASC);

        return $itemCollection;
    }
}
