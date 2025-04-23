<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Block\Adminhtml\Builder;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Model\Backend\Builder\GetItemCollection;
use Amasty\MegaMenuLite\Model\Backend\Builder\Registry;
use Amasty\MegaMenuLite\Model\OptionSource\Status;
use Amasty\MegaMenuLite\Model\OptionSource\UrlKey;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\CollectionFactory;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position\Collection;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\Store;

class Content extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_MegaMenuLite::builder/content.phtml';

    /**
     * @var CategoryCollection
     */
    private $categoryCollection;

    /**
     * @var \Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Collection
     */
    private $itemsCollection;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var CollectionFactory
     */
    private $itemsCollectionFactory;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var GetItemCollection
     */
    private $getItemCollection;

    /**
     * @var UrlKey
     */
    private $urlKey;

    public function __construct(
        Context $context,
        CollectionFactory $itemsCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        Registry $registry,
        GetItemCollection $getItemCollection,
        ?UrlKey $urlKey = null,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->itemsCollectionFactory = $itemsCollectionFactory;
        $this->registry = $registry;
        $this->getItemCollection = $getItemCollection;
        $this->urlKey = $urlKey ?? ObjectManager::getInstance()->get(UrlKey::class);
    }

    /**
     * @return Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getItems(): Collection
    {
        return $this->getItemCollection->execute();
    }

    /**
     * @return string
     */
    public function getMoveUrl()
    {
        return $this->getUrl('*/*/move');
    }

    /**
     * @param ItemInterface $item
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getContentItem($item)
    {
        $entityId = $item->getEntityId();
        switch ($item->getType()) {
            case 'category':
                $collection = $this->getCategoryCollection();
                break;
            case 'custom':
            default:
                $collection = $this->getItemsCollection();
                break;
        }

        return $collection->getItemByColumnValue('entity_id', $entityId);
    }

    /**
     * @return CategoryCollection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCategoryCollection()
    {
        if ($this->categoryCollection === null) {
            /** @var CategoryCollection $collection */
            $this->categoryCollection = $this->categoryCollectionFactory->create();
            $this->categoryCollection->setStoreId($this->registry->getStoreId());
            $this->categoryCollection->addAttributeToSelect('name');
            $this->categoryCollection->addFieldToFilter(
                'path',
                ['like' => '1/' . $this->getRootCategoryId() . '/%']
            ); //load only from store root
            $this->categoryCollection->addAttributeToFilter('include_in_menu', 1);
            $this->categoryCollection->addIsActiveFilter();
        }

        return $this->categoryCollection;
    }

    /**
     * @return \Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Collection
     */
    private function getItemsCollection()
    {
        if ($this->itemsCollection === null) {
            $allowedUrlKeys = $this->urlKey->getValues();
            $this->itemsCollection = $this->itemsCollectionFactory->create();
            $this->itemsCollection->addFieldToFilter('store_id', Store::DEFAULT_STORE_ID)
                ->addFieldToFilter(ItemInterface::TYPE, ItemInterface::CUSTOM_TYPE);

            $storeCollection = $this->itemsCollectionFactory->create()
                ->addFieldToFilter('store_id', $this->registry->getStoreId())
                ->addFieldToFilter(ItemInterface::TYPE, ItemInterface::CUSTOM_TYPE);
            foreach ($this->itemsCollection->getItems() as $key => $item) {
                $storeModel = $storeCollection->getCustomItemByEntityId($item->getEntityId());
                if ($storeModel) {
                    $item->addData(
                        array_filter(
                            $storeModel->getData(),
                            static function ($var) {
                                return $var !== null;
                            }
                        )
                    );
                }

                if ($item->getStatus() == Status::DISABLED) {
                    $this->itemsCollection->removeItemByKey($key);
                }

                if (!in_array((int)$item->getLinkType(), $allowedUrlKeys, true)) {
                    $this->itemsCollection->removeItemByKey($key);
                }
            }
        }

        return $this->itemsCollection;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getRootCategoryId()
    {
        return $this->_storeManager->getStore($this->registry->getStoreId())->getRootCategoryId();
    }
}
