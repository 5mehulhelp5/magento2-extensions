<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Menu;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Item extends AbstractModel implements ItemInterface, IdentityInterface
{
    public const CACHE_TAG = 'amasty_mega_menu';

    /**
     * @var string
     */
    protected $_eventPrefix = 'mega_menu_content';

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item::class);
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG, self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getId(): ?int
    {
        return (int) $this->_getData(ItemInterface::ID) ?: null;
    }

    public function setId($id)
    {
        return $this->setData(ItemInterface::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId()
    {
        return $this->_getData(ItemInterface::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId)
    {
        return $this->setData(ItemInterface::ENTITY_ID, $entityId);
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->_getData(ItemInterface::TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->setData(ItemInterface::TYPE, $type);
    }

    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        return $this->_getData(ItemInterface::STORE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStoreId($storeId)
    {
        $this->setData(ItemInterface::STORE_ID, $storeId);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->_getData(ItemInterface::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->setData(ItemInterface::NAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return $this->_getData(ItemInterface::LABEL);
    }

    /**
     * @inheritdoc
     */
    public function setLabel($label)
    {
        $this->setData(ItemInterface::LABEL, $label);
    }

    /**
     * @inheritdoc
     */
    public function getLabelTextColor()
    {
        return $this->_getData(ItemInterface::LABEL_TEXT_COLOR);
    }

    /**
     * @inheritdoc
     */
    public function setLabelTextColor($labelColor)
    {
        $this->setData(ItemInterface::LABEL_TEXT_COLOR, $labelColor);
    }

    /**
     * @inheritdoc
     */
    public function getLabelBackgroundColor()
    {
        return $this->_getData(ItemInterface::LABEL_BACKGROUND_COLOR);
    }

    /**
     * @inheritdoc
     */
    public function setLabelBackgroundColor($labelColor)
    {
        $this->setData(ItemInterface::LABEL_BACKGROUND_COLOR, $labelColor);
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        $status = $this->_getData(ItemInterface::STATUS);

        return $status !== null ? (int) $status : null;
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        $this->setData(ItemInterface::STATUS, $status);
    }

    /**
     * @deprecated
     */
    public function getLink()
    {
        return $this->_getData(ItemInterface::LINK);
    }

    public function setLink($link)
    {
        $this->setData(ItemInterface::LINK, $link);

        return $this;
    }

    public function getLinkType(): int
    {
        return (int) $this->_getData(ItemInterface::LINK_TYPE);
    }

    public function setLinkType(int $linkType): void
    {
        $this->setData(ItemInterface::LINK_TYPE, $linkType);
    }

    public function getUseDefault(): ?string
    {
        return $this->_getData(self::USE_DEFAULT);
    }

    public function setUseDefault(?string $useDefault): void
    {
        $this->setData(self::USE_DEFAULT, $useDefault);
    }
}
