<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Product;

use Amasty\Xnotif\Model\Product\Subscribe\PrepareModelForSave;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\ProductAlert\Model\ResourceModel\Stock;
use Magento\ProductAlert\Model\ResourceModel\Stock\Collection;
use Magento\ProductAlert\Model\ResourceModel\Stock\CollectionFactory;
use Magento\ProductAlert\Model\StockFactory;
use Magento\Store\Model\StoreManagerInterface;

class StockSubscribe
{
    public const STOCK = 'stock';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var StockFactory
     */
    private $stockFactory;

    /**
     * @var CollectionFactory
     */
    private $stockCollectionFactory;

    /**
     * @var PrepareModelForSave
     */
    private $prepareModelForSave;

    /**
     * @var Stock
     */
    private $stockResource;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    public function __construct(
        StockFactory $stockFactory,
        CollectionFactory $stockCollectionFactory,
        StoreManagerInterface $storeManager,
        PrepareModelForSave $prepareModelForSave,
        Stock $stockResource,
        EventManagerInterface $eventManager = null // TODO move to not optional
    ) {
        $this->stockFactory = $stockFactory;
        $this->stockCollectionFactory = $stockCollectionFactory;
        $this->storeManager = $storeManager;
        $this->prepareModelForSave = $prepareModelForSave;
        $this->stockResource = $stockResource;
        $this->eventManager = $eventManager ?? ObjectManager::getInstance()->get(EventManagerInterface::class);
    }

    /**
     * @param int $productId
     * @param string|null $guestEmail
     * @param int|null $parentId
     * @return int
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(int $productId, ?string $guestEmail = null, ?int $parentId = null): int
    {
        $parentId = $parentId ?: $productId;
        $websiteId = (int)$this->storeManager->getStore()->getWebsiteId();
        $collection = $this->getStockCollection($websiteId, $productId);
        $model = $this->stockFactory->create();
        list($model, $status) = $this->prepareModelForSave
            ->execute($productId, $model, $collection, $guestEmail, $parentId);

        if ($model) {
            $this->stockResource->save($model);
            $this->eventManager->dispatch(
                'amasty_xnotif_after_save_stock_model',
                ['model' => $model, 'model_type' => self::STOCK]
            );
        }

        return $status;
    }

    private function getStockCollection(int $websiteId, int $productId): Collection
    {
        return $this->stockCollectionFactory->create()
            ->addWebsiteFilter($websiteId)
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('status', 0)
            ->setCustomerOrder();
    }
}
