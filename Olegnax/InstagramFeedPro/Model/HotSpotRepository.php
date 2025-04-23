<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Model;

use Exception;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Olegnax\InstagramFeedPro\Api\Data\HotSpotInterface;
use Olegnax\InstagramFeedPro\Api\Data\HotSpotInterfaceFactory;
use Olegnax\InstagramFeedPro\Api\Data\HotSpotSearchResultsInterfaceFactory;
use Olegnax\InstagramFeedPro\Api\HotSpotRepositoryInterface;
use Olegnax\InstagramFeedPro\Model\ResourceModel\HotSpot as ResourceHotSpot;
use Olegnax\InstagramFeedPro\Model\ResourceModel\HotSpot\CollectionFactory as HotSpotCollectionFactory;

class HotSpotRepository implements HotSpotRepositoryInterface
{

    protected $dataObjectProcessor;

    protected $extensibleDataObjectConverter;
    protected $dataHotSpotFactory;
    protected $searchResultsFactory;
    protected $hotSpotCollectionFactory;
    protected $resource;
    protected $dataObjectHelper;
    protected $extensionAttributesJoinProcessor;
    protected $hotSpotFactory;
    private $collectionProcessor;
    private $storeManager;

    /**
     * @param ResourceHotSpot $resource
     * @param HotSpotFactory $hotSpotFactory
     * @param HotSpotInterfaceFactory $dataHotSpotFactory
     * @param HotSpotCollectionFactory $hotSpotCollectionFactory
     * @param HotSpotSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceHotSpot $resource,
        HotSpotFactory $hotSpotFactory,
        HotSpotInterfaceFactory $dataHotSpotFactory,
        HotSpotCollectionFactory $hotSpotCollectionFactory,
        HotSpotSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->hotSpotFactory = $hotSpotFactory;
        $this->hotSpotCollectionFactory = $hotSpotCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataHotSpotFactory = $dataHotSpotFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        HotSpotInterface $hotSpot
    ) {
        /* if (empty($hotSpot->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $hotSpot->setStoreId($storeId);
        } */

        $hotSpotData = $this->extensibleDataObjectConverter->toNestedArray(
            $hotSpot,
            [],
            HotSpotInterface::class
        );

        $hotSpotModel = $this->hotSpotFactory->create()->setData($hotSpotData);

        try {
            $this->resource->save($hotSpotModel);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the hotSpot: %1',
                $exception->getMessage()
            ));
        }
        return $hotSpotModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        SearchCriteriaInterface $criteria
    ) {
        $collection = $this->hotSpotCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            HotSpotInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($hotSpotId)
    {
        return $this->delete($this->get($hotSpotId));
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        HotSpotInterface $hotSpot
    ) {
        try {
            $hotSpotModel = $this->hotSpotFactory->create();
            $this->resource->load($hotSpotModel, $hotSpot->getHotspotId());
            $this->resource->delete($hotSpotModel);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the HotSpot: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($hotSpotId)
    {
        $hotSpot = $this->hotSpotFactory->create();
        $this->resource->load($hotSpot, $hotSpotId);
        if (!$hotSpot->getId()) {
            throw new NoSuchEntityException(__('HotSpot with id "%1" does not exist.', $hotSpotId));
        }
        return $hotSpot->getDataModel();
    }
}

