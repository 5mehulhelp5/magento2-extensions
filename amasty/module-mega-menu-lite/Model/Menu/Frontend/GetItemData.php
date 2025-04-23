<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Menu\Frontend;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenuLite\Model\OptionSource\UrlKey;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class GetItemData
{
    public const IS_ACTIVE = 'is_active';

    public const HAS_ACTIVE = 'has_active';

    public const IS_CATEGORY = 'is_category';

    public const ITEM_POSITION_CLASS_PREFIX = 'nav-';

    public const CUSTOM_NODE_PREFIX = 'custom-node-';

    public const CATEGORY_NODE_PREFIX = 'category-node-';

    public const ADDITIONAL_NODE_PREFIX = 'additional-node-';

    public const LEVEL_DIFF = 2;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var LayerResolver
     */
    private $layerResolver;

    /**
     * @var CategoryHelper
     */
    private $categoryHelper;

    /**
     * @var array|null
     */
    private $currentCategoryPath = null;

    /**
     * @var int|null
     */
    private $currentCategoryId = null;

    /**
     * @var int
     */
    private $positionCounter = 0;

    public function __construct(
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        LayerResolver $layerResolver,
        CategoryHelper $categoryHelper
    ) {
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->layerResolver = $layerResolver;
        $this->categoryHelper = $categoryHelper;
        $this->initCurrentCategory();
    }

    public function execute(DataObject $item): array
    {
        switch (true) {
            case $item instanceof CategoryInterface:
                $result = $this->getCategoryData($item);
                break;
            case $item instanceof LinkInterface:
                $result = $this->getItemData($item);
                break;
            default:
                $result = $this->getAdditionalLinkData($item);
        }

        return $result;
    }

    private function getItemData(LinkInterface $item): array
    {
        $itemData = $item->getData();
        $linkType = $item->getLinkType();
        $url = $item->getUrl() ?? '';
        $url = $linkType == UrlKey::EXTERNAL_URL || $linkType == UrlKey::NO
            ? $url
            : $this->getAbsoluteUrl($url);

        $additionalData = [
            ItemInterface::NAME => $item->getName(),
            ItemInterface::ID => self::CUSTOM_NODE_PREFIX . $item->getEntityId(),
            self::HAS_ACTIVE => false,
            self::IS_ACTIVE => $this->isItemActive($url),
            self::IS_CATEGORY => false,
            ItemInterface::LINK_TYPE => $linkType,
            ItemInterface::STATUS => $item->getStatus(),
            'url' => $url,
            'width' => $item->getWidth(),
            'content' => $item->getContent(),
            'is_parent_active' => true
        ];

        return array_merge($itemData, $additionalData);
    }

    private function getCategoryData(CategoryInterface $category): array
    {
        $categoryData = $category->getData();
        $additionalData = [
            ItemInterface::ID => self::CATEGORY_NODE_PREFIX . $category->getId(),
            self::HAS_ACTIVE => in_array(
                (string)$category->getId(),
                $this->currentCategoryPath,
                true
            ),
            self::IS_ACTIVE => $category->getId() == $this->currentCategoryId,
            self::IS_CATEGORY => true,
            'url' => $this->categoryHelper->getCategoryUrl($category),
            'is_parent_active' => true,
            'level' => $category->getLevel() - self::LEVEL_DIFF,
            'position_class'  => $this->getPositionClass()
        ];

        return array_merge($categoryData, $additionalData);
    }

    private function getAdditionalLinkData(DataObject $additionalLink): array
    {
        $additionalLinkData = $additionalLink->getData();
        $additionalData = [
            ItemInterface::NAME => $additionalLink->getName(),
            ItemInterface::ID => self::ADDITIONAL_NODE_PREFIX . $additionalLink->getId(),
            self::HAS_ACTIVE=> false,
            self::IS_ACTIVE => $this->isItemActive($additionalLink->getUrl()),
            self::IS_CATEGORY => false,
            'url' => $additionalLink->getUrl(),
            'width' => (int)$additionalLink->getWidth(),
            'content' => $additionalLink->getContent(),
            'is_parent_active' => true
        ];

        return array_merge($additionalLinkData, $additionalData);
    }

    private function getPositionClass(): string
    {
        return self::ITEM_POSITION_CLASS_PREFIX . $this->positionCounter++;
    }

    private function getAbsoluteUrl(string $url, string $type = UrlInterface::URL_TYPE_LINK)
    {
        return $this->getStoreBaseUrl($type) . ltrim($url, '/');
    }

    private function getStoreBaseUrl(string $type = UrlInterface::URL_TYPE_LINK)
    {
        $store = $this->storeManager->getStore();
        $isSecure = $store->isUrlSecure();

        return rtrim($store->getBaseUrl($type, $isSecure), '/') . '/';
    }

    private function isItemActive(string $url): bool
    {
        if ($url) {
            $result = strpos($this->urlBuilder->getCurrentUrl(), $url) !== false;
        }

        return $result ?? false;
    }

    private function initCurrentCategory(): void
    {
        $catalogLayer = $this->layerResolver->get();

        if ($catalogLayer) {
            $currentCategory = $catalogLayer->getCurrentCategory();
            $this->currentCategoryId = $currentCategory->getId();
            $this->currentCategoryPath = explode('/', $currentCategory->getPath());
        }
    }
}
