<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Repository;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Api\ItemRepositoryInterface;
use Amasty\MegaMenuLite\Model\Menu\ItemFactory;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item as ItemResource;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Collection;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\CollectionFactory;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ItemRepository implements ItemRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var ItemFactory
     */
    protected $itemFactory;

    /**
     * @var ItemResource
     */
    protected $itemResource;

    /**
     * Model data storage
     *
     * @var array
     */
    protected $items = [];

    /**
     * Model data storage
     *
     * @var array
     */
    protected $itemsByEntityId = [];

    /**
     * @var CollectionFactory
     */
    protected $itemCollectionFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        ItemFactory $itemFactory,
        ItemResource $itemResource,
        CollectionFactory $itemCollectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->itemFactory = $itemFactory;
        $this->itemResource = $itemResource;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     */
    public function save(ItemInterface $item)
    {
        try {
            if ($item->getId()) {
                $item = $this->getById($item->getId())->addData($item->getData());
            }
            $this->itemResource->save($item);
            unset($this->items[$item->getId()]);
        } catch (\Exception $e) {
            if ($item->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save item with ID %1. Error: %2',
                        [$item->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new item. Error: %1', $e->getMessage()));
        }

        return $item;
    }

    /**
     * @inheritdoc
     */
    public function getNew(): ItemInterface
    {
        return $this->itemFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        if (!isset($this->items[$id])) {
            /** @var \Amasty\MegaMenuLite\Model\Menu\Item $item */
            $item = $this->itemFactory->create();
            $this->itemResource->load($item, $id);
            if (!$item->getId()) {
                throw new NoSuchEntityException(__('Item with specified ID "%1" not found.', $item->getEntityId()));
            }
            $this->items[$id] = $item;
            $this->itemsByEntityId[$item->getType()][$item->getStoreId()][$item->getEntityId()] = $item;
        }

        return $this->items[$id];
    }

    /**
     * @inheritdoc
     */
    public function getByEntityId($entityId, $storeId, $type)
    {
        if (!isset($this->itemsByEntityId[$type][$storeId][$entityId])) {
            $this->searchCriteriaBuilder
                ->addFilter(ItemInterface::ENTITY_ID, $entityId)
                ->addFilter(ItemInterface::STORE_ID, $storeId)
                ->addFilter(ItemInterface::TYPE, $type);
            $items = $this->getList($this->searchCriteriaBuilder->create())->getItems();
            if (isset($items[0])) {
                $this->items[$items[0]->getId()] = $items[0];
                $this->itemsByEntityId[$type][$storeId][$entityId] = $items[0];
            } else {
                $this->itemsByEntityId[$type][$storeId][$entityId] = null;
            }
        }

        return $this->itemsByEntityId[$type][$storeId][$entityId];
    }

    /**
     * @inheritdoc
     */
    public function delete(ItemInterface $item)
    {
        try {
            $this->itemResource->deleteItem($item->getType(), $item->getEntityId());
            unset($this->items[$item->getId()]);
        } catch (\Exception $e) {
            if ($item->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove item with ID %1. Error: %2',
                        [$item->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove item. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($entityId)
    {
        $itemModel = $this->getById($entityId);
        $this->delete($itemModel);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Collection $itemCollection */
        $itemCollection = $this->itemCollectionFactory->create();

        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $itemCollection);
        }

        $searchResults->setTotalCount($itemCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $itemCollection);
        }

        $itemCollection->setCurPage($searchCriteria->getCurrentPage());
        $itemCollection->setPageSize($searchCriteria->getPageSize());

        $items = [];
        /** @var ItemInterface $item */
        foreach ($itemCollection->getItems() as $item) {
            $items[] = $this->getById($item->getId());
        }

        $searchResults->setItems($items);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $itemCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $itemCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $itemCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection  $itemCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $itemCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $itemCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? SortOrder::SORT_DESC : SortOrder::SORT_ASC
            );
        }
    }
}
