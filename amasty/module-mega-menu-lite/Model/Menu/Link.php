<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Menu;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenuLite\Api\ItemRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\Store;

class Link extends AbstractModel implements LinkInterface
{
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Amasty\MegaMenuLite\Model\ResourceModel\Menu\Link::class);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId(): int
    {
        return (int) $this->_getData(LinkInterface::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId)
    {
        $this->setData(LinkInterface::ENTITY_ID, $entityId);

        return $this;
    }

    /**
     * @deprecated
     */
    public function getLink()
    {
        /** @var ItemRepositoryInterface $itemRepository */
        $itemRepository = ObjectManager::getInstance()->get(ItemRepositoryInterface::class);
        $item = $itemRepository->getByEntityId(
            (int)$this->getEntityId(),
            Store::DEFAULT_STORE_ID,
            ItemInterface::CUSTOM_TYPE
        );

        if (!$item) {
            return '';
        }

        return $item->getLink();
    }

    /**
     * @deprecated
     */
    public function setLink($link)
    {
        /** @var ItemRepositoryInterface $itemRepository */
        $itemRepository = ObjectManager::getInstance()->get(ItemRepositoryInterface::class);
        $item = $itemRepository->getByEntityId(
            (int)$this->getEntityId(),
            Store::DEFAULT_STORE_ID,
            ItemInterface::CUSTOM_TYPE
        );

        if (!$item) {
            return $this;
        }

        $item->setLink($link);
        $itemRepository->save($item);

        return $this;
    }

    /**
     * @deprecated
     */
    public function getLinkType(): int
    {
        /** @var ItemRepositoryInterface $itemRepository */
        $itemRepository = ObjectManager::getInstance()->get(ItemRepositoryInterface::class);
        $item = $itemRepository->getByEntityId(
            (int)$this->getEntityId(),
            Store::DEFAULT_STORE_ID,
            ItemInterface::CUSTOM_TYPE
        );

        if (!$item) {
            return 0;
        }

        return $item->getLinkType();
    }

    /**
     * @deprecated
     */
    public function setLinkType(int $linkType): void
    {
        /** @var ItemRepositoryInterface $itemRepository */
        $itemRepository = ObjectManager::getInstance()->get(ItemRepositoryInterface::class);
        $item = $itemRepository->getByEntityId(
            (int)$this->getEntityId(),
            Store::DEFAULT_STORE_ID,
            ItemInterface::CUSTOM_TYPE
        );

        if (!$item) {
            return;
        }

        $item->setLinkType($linkType);
        $itemRepository->save($item);
    }

    public function getParentId(): int
    {
        return (int) $this->_getData(self::PARENT_ID);
    }

    public function setParentId(?int $parentId): void
    {
        $this->setData(self::PARENT_ID, $parentId);
    }

    public function getPath(): ?string
    {
        return $this->_getData(self::PATH);
    }

    public function setPath(?string $path): void
    {
        $this->setData(self::PATH, $path);
    }

    public function getLevel(): int
    {
        return (int) $this->_getData(self::LEVEL);
    }

    public function setLevel(int $level): void
    {
        $this->setData(self::LEVEL, $level);
    }
}
