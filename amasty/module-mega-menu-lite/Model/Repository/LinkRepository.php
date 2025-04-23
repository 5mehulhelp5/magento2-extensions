<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Repository;

use Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenuLite\Api\ItemRepositoryInterface;
use Amasty\MegaMenuLite\Api\LinkRepositoryInterface;
use Amasty\MegaMenuLite\Model\Menu\LinkFactory;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position as PositionResource;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Link as LinkResource;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Link\Collection;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Link\CollectionFactory;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LinkRepository implements LinkRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var LinkFactory
     */
    private $linkFactory;

    /**
     * @var LinkResource
     */
    private $linkResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $links;

    /**
     * @var CollectionFactory
     */
    private $linkCollectionFactory;

    /**
     * @var ItemRepositoryInterface
     */
    private $itemRepository;

    /**
     * @var PositionResource
     */
    private $positionResource;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        LinkFactory $linkFactory,
        LinkResource $linkResource,
        PositionResource $positionResource,
        CollectionFactory $linkCollectionFactory,
        ItemRepositoryInterface $itemRepository
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->linkFactory = $linkFactory;
        $this->linkResource = $linkResource;
        $this->linkCollectionFactory = $linkCollectionFactory;
        $this->itemRepository = $itemRepository;
        $this->positionResource = $positionResource;
    }

    /**
     * @inheritdoc
     */
    public function save(LinkInterface $link)
    {
        try {
            if ($link->getEntityId()) {
                $link = $this->getById($link->getEntityId())->addData($link->getData());
            }
            $this->linkResource->save($link);
            unset($this->links[$link->getEntityId()]);
        } catch (\Exception $e) {
            if ($link->getEntityId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save link with ID %1. Error: %2',
                        [$link->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new link. Error: %1', $e->getMessage()));
        }

        return $link;
    }

    /**
     * @inheritdoc
     */
    public function getNew(): LinkInterface
    {
        return $this->linkFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function getById($entityId)
    {
        if (!isset($this->links[$entityId])) {
            /** @var \Amasty\MegaMenuLite\Model\Menu\Link $link */
            $link = $this->linkFactory->create();
            $this->linkResource->load($link, $entityId);
            if (!$link->getEntityId()) {
                throw new NoSuchEntityException(__('Link with specified ID "%1" not found.', $entityId));
            }
            $this->links[$entityId] = $link;
        }

        return $this->links[$entityId];
    }

    public function isEntityExist(int $entityId): bool
    {
        return $this->linkResource->isEntityExist($entityId);
    }

    /**
     * @inheritdoc
     */
    public function delete(LinkInterface $link)
    {
        try {
            $item = $this->itemRepository->getByEntityId($link->getEntityId(), $link->getStoreId(), 'custom');
            $this->linkResource->delete($link);
            $this->itemRepository->delete($item);
            $this->positionResource->deleteItem($item->getType(), $item->getEntityId());
            unset($this->links[$link->getEntityId()]);
        } catch (\Exception $e) {
            if ($link->getEntityId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove link with ID %1. Error: %2',
                        [$link->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove link. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($entityId)
    {
        $linkModel = $this->getById($entityId);
        $this->delete($linkModel);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Amasty\MegaMenuLite\Model\ResourceModel\Menu\Link\Collection $linkCollection */
        $linkCollection = $this->linkCollectionFactory->create();

        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $linkCollection);
        }

        $searchResults->setTotalCount($linkCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $linkCollection);
        }

        $linkCollection->setCurPage($searchCriteria->getCurrentPage());
        $linkCollection->setPageSize($searchCriteria->getPageSize());

        $links = [];
        /** @var LinkInterface $link */
        foreach ($linkCollection->getItems() as $link) {
            $links[] = $this->getById($link->getEntityId());
        }

        $searchResults->setItems($links);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $linkCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $linkCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $linkCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection  $linkCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $linkCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $linkCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? SortOrder::SORT_DESC : SortOrder::SORT_ASC
            );
        }
    }
}
