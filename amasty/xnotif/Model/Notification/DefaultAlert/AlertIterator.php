<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Notification\DefaultAlert;

use Amasty\Xnotif\Model\ConfigProvider;
use Amasty\Xnotif\Model\Notification\DefaultAlert\Service\CustomerService;
use Amasty\Xnotif\Model\Notification\DefaultAlert\Service\ProductService;
use Generator;
use Magento\Framework\Data\Collection;
use Magento\Store\Model\StoreManagerInterface;

class AlertIterator
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var GetCollectionByType
     */
    private $getCollectionByType;

    /**
     * @var CustomerService
     */
    private $customerService;

    /**
     * @var ProductService
     */
    private $productService;

    /**
     * @var int
     */
    private $batchSize;

    public function __construct(
        StoreManagerInterface $storeManager,
        ConfigProvider $configProvider,
        GetCollectionByType $getCollectionByType,
        CustomerService $customerService,
        ProductService $productService,
        int $batchSize = 1000
    ) {
        $this->storeManager = $storeManager;
        $this->configProvider = $configProvider;
        $this->getCollectionByType = $getCollectionByType;
        $this->customerService = $customerService;
        $this->productService = $productService;
        $this->batchSize = $batchSize;
    }

    public function getItems(string $alertType): Generator
    {
        foreach ($this->storeManager->getStores() as $store) {
            $storeId = (int)$store->getId();
            if ($this->isAlertEnabled($alertType, $storeId)) {
                continue;
            }

            $collection = $this->getCollectionByType->execute($alertType, null, $storeId);
            yield from [$storeId => $this->collectionBatchIterator($collection)];
        }

        foreach ($this->storeManager->getWebsites() as $website) {
            if (!$website->getDefaultGroup() || !$website->getDefaultGroup()->getDefaultStore()) {
                continue;
            }

            $storeId = (int)$website->getDefaultGroup()->getDefaultStore()->getId();
            if ($this->isAlertEnabled($alertType, $storeId)) {
                continue;
            }

            $collection = $this->getCollectionByType->execute($alertType, (int)$website->getId());
            yield from [$storeId => $this->collectionBatchIterator($collection)];
        }
    }

    private function collectionBatchIterator(Collection $collection): Generator
    {
        $collection->setPageSize($this->batchSize);

        $lastPageNumber = $collection->getLastPageNumber();
        for ($page = 1; $page <= $lastPageNumber; $page++) {
            $collection->clear();
            $collection->setCurPage($page);

            $this->customerService->preload($collection);
            $this->productService->preload($collection);

            yield from $collection->getItems();
        }
    }

    private function isAlertEnabled(string $alertType, int $storeId): bool
    {
        return $this->configProvider->isAlertEnabled($alertType, $storeId);
    }
}
