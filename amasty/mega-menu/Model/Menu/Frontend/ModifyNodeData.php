<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\Menu\Frontend;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Model\Menu\Subcategory;
use Amasty\MegaMenu\Model\OptionSource\Font;
use Amasty\MegaMenu\Model\OptionSource\SubcategoriesPosition;
use Amasty\MegaMenuLite\Model\Menu\Frontend\ModifyNodeDataInterface;
use Amasty\MegaMenuLite\ViewModel\Store\Menu;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class ModifyNodeData implements ModifyNodeDataInterface
{
    public const MAX_COLUMN_COUNT = 10;

    public const DEFAULT_COLUMN_COUNT = 4;

    /**
     * @var SubcategoriesPosition
     */
    private $subcategoriesPosition;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        SubcategoriesPosition $subcategoriesPosition,
        StoreManagerInterface $storeManager
    ) {
        $this->subcategoriesPosition = $subcategoriesPosition;
        $this->storeManager = $storeManager;
    }

    public function execute(Node $node, array $data): array
    {
        $additionalData = [
            ItemInterface::TYPE => $this->getNodeType($node),
            ItemInterface::SUBMENU_TYPE => (bool) $node->getData(ItemInterface::SUBMENU_TYPE),
            ItemInterface::WIDTH => (int) $node->getData(ItemInterface::WIDTH),
            ItemInterface::WIDTH_VALUE => (int) $node->getData(ItemInterface::WIDTH_VALUE),
            ItemInterface::COLUMN_COUNT => $this->getColumnCount($node),
            ItemInterface::HIDE_CONTENT => (bool) $node->getData(ItemInterface::HIDE_CONTENT),
            ItemInterface::DESKTOP_FONT => $node->getData(ItemInterface::DESKTOP_FONT) ?? Font::BOLD,
            ItemInterface::MOBILE_FONT => $node->getData(ItemInterface::MOBILE_FONT) ?? Font::BOLD
        ];

        if ($node->getData(ItemInterface::ICON)) {
            $additionalData[ItemInterface::ICON] = $this->getIcon($node);
            $additionalData[Menu::ICON_EXTENSION] = $this->getIconExtension($additionalData[ItemInterface::ICON]);
        }
        $data = array_merge($additionalData, $data);
        if ($data[ItemInterface::HIDE_CONTENT]) {
            unset($data[ItemInterface::CONTENT]);
        }

        return $data;
    }

    public function getIcon(Node $node): string
    {
        $url = '';
        if ($node->getIcon()) {
            $url = $this->validateIconPath($node->getIcon());
            $url = $this->getMediaBaseUrl() . $url;
        }

        return $url;
    }

    private function getIconExtension(string $icon): string
    {
        $iconParts = explode('.', $icon);

        return end($iconParts);
    }

    private function getNodeType(Node $node): ?array
    {
        $options = $this->subcategoriesPosition->toOptionArray(true);
        $position = $node->getData(ItemInterface::SUBCATEGORIES_POSITION);
        if ($position === null) {
            $position = $this->getDefaultPosition((int) $node->getData('level'));
        }

        if (isset($options[$position]['label'])) {
            $type = $options[$position]['label']->getText();
            $type = [
                'value' => (int) $position,
                'label' => strtolower($type)
            ];
        }

        return $type ?? null;
    }

    private function getDefaultPosition(int $level): int
    {
        return SubcategoriesPosition::LEFT;
    }

    private function getMediaBaseUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    private function validateIconPath(string $path): string
    {
        //TODO make refactor not to save "media" in icon data
        $path = preg_replace(
            "/^\/media/",
            "",
            $path
        );
        $path = str_replace(' ', '%20', $path);
        $path = ltrim($path, '/');

        return $path;
    }

    public function getColumnCount(Node $node): int
    {
        $count = $node->getColumnCount() !== null ? (int)$node->getColumnCount() : self::DEFAULT_COLUMN_COUNT;

        return min($count, static::MAX_COLUMN_COUNT);
    }
}
