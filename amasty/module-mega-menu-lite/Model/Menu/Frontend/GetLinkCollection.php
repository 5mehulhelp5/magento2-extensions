<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Menu\Frontend;

use Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenuLite\Model\Provider\FieldsByStore;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Link\Collection as LinkCollection;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Link\CollectionFactory as LinkCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class GetLinkCollection
{
    /**
     * @var LinkCollectionFactory
     */
    private $linkCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var FieldsByStore
     */
    private $fieldsByStore;

    public function __construct(
        LinkCollectionFactory $linkCollectionFactory,
        StoreManagerInterface $storeManager,
        FieldsByStore $fieldsByStore
    ) {
        $this->linkCollectionFactory = $linkCollectionFactory;
        $this->storeManager = $storeManager;
        $this->fieldsByStore = $fieldsByStore;
    }

    public function execute(): LinkCollection
    {
        $linkCollection = $this->linkCollectionFactory->create();
        $storeId = (int) $this->storeManager->getStore()->getId();

        $linkCollection->joinItemContent($storeId, $this->getCols());
        $linkCollection->joinItemOrder($storeId, [Position::POSITION]);
        $linkCollection->excludeDisabled($storeId);
        $linkCollection->addOrder(LinkInterface::LEVEL, LinkCollection::SORT_ORDER_ASC);
        $linkCollection->addOrder(Position::POSITION, LinkCollection::SORT_ORDER_ASC);
        $linkCollection->addOrder(LinkInterface::PARENT_ID, LinkCollection::SORT_ORDER_ASC);
        $linkCollection->sortByEntityId();
        $linkCollection->addFieldsToSelect();
        $linkCollection->addUrlToSelect($storeId);

        return $linkCollection;
    }

    private function getCols(): array
    {
        $result = [];
        $fieldsets = $this->fieldsByStore->getCustomFields();
        array_walk_recursive($fieldsets, function ($item, $key) use (&$result) {
            if ($item !== Position::POSITION) {
                $result[] = $item;
            }
        });

        return $result;
    }
}
