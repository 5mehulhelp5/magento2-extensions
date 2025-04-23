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
use Olegnax\InstagramFeedPro\Api\Data\IntsUserInterface;
use Olegnax\InstagramFeedPro\Api\Data\IntsUserInterfaceFactory;
use Olegnax\InstagramFeedPro\Api\Data\IntsUserSearchResultsInterfaceFactory;
use Olegnax\InstagramFeedPro\Api\IntsUserRepositoryInterface;
use Olegnax\InstagramFeedPro\Model\ResourceModel\IntsUser as ResourceIntsUser;
use Olegnax\InstagramFeedPro\Model\ResourceModel\IntsUser\CollectionFactory as IntsUserCollectionFactory;

class IntsUserRepository implements IntsUserRepositoryInterface
{

    protected $intsUserCollectionFactory;

    protected $resource;

    protected $dataObjectHelper;

    protected $extensibleDataObjectConverter;
    protected $intsUserFactory;
    protected $dataObjectProcessor;
    protected $searchResultsFactory;
    protected $dataIntsUserFactory;
    protected $extensionAttributesJoinProcessor;
    private $storeManager;
    private $collectionProcessor;

    /**
     * @param ResourceIntsUser $resource
     * @param IntsUserFactory $intsUserFactory
     * @param IntsUserInterfaceFactory $dataIntsUserFactory
     * @param IntsUserCollectionFactory $intsUserCollectionFactory
     * @param IntsUserSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceIntsUser $resource,
        IntsUserFactory $intsUserFactory,
        IntsUserInterfaceFactory $dataIntsUserFactory,
        IntsUserCollectionFactory $intsUserCollectionFactory,
        IntsUserSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->intsUserFactory = $intsUserFactory;
        $this->intsUserCollectionFactory = $intsUserCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataIntsUserFactory = $dataIntsUserFactory;
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
        IntsUserInterface $intsUser
    ) {
        $intsUserData = $this->extensibleDataObjectConverter->toNestedArray(
            $intsUser,
            [],
            IntsUserInterface::class
        );

        $intsUserModel = $this->intsUserFactory->create()->setData($intsUserData);

        try {
            $this->resource->save($intsUserModel);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the intsUser: %1',
                $exception->getMessage()
            ));
        }
        return $intsUserModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        SearchCriteriaInterface $criteria
    ) {
        $collection = $this->intsUserCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            IntsUserInterface::class
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
    public function deleteById($intsUserId)
    {
        return $this->delete($this->get($intsUserId));
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        IntsUserInterface $intsUser
    ) {
        try {
            $intsUserModel = $this->intsUserFactory->create();
            $this->resource->load($intsUserModel, $intsUser->getIntsuserId());
            $this->resource->delete($intsUserModel);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the IntsUser: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($intsUserId)
    {
        $intsUser = $this->intsUserFactory->create();
        $this->resource->load($intsUser, $intsUserId);
        if (!$intsUser->getId()) {
            throw new NoSuchEntityException(__('IntsUser with id "%1" does not exist.', $intsUserId));
        }
        return $intsUser->getDataModel();
    }
}
