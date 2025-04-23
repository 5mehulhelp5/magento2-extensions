<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Ui\DataProvider\Form\Category\Modifier;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Model\Backend\Ui\HideField;
use Amasty\MegaMenu\Model\Menu\Subcategory;
use Amasty\MegaMenu\Model\Provider\FieldsToHideProvider;
use Magento\Catalog\Model\Category;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\Store;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class HideItems implements ModifierInterface
{
    /**
     * @var Category
     */
    private $entity;

    /**
     * @var int
     */
    private $parentId;

    /**
     * @var FieldsToHideProvider
     */
    private $fieldsToHideProvider;

    /**
     * @var HideField
     */
    private $hideField;

    public function __construct(
        RequestInterface $request,
        FieldsToHideProvider $fieldsToHideProvider,
        HideField $hideField
    ) {
        $this->parentId = (int) $request->getParam('parent', 0);
        $this->fieldsToHideProvider = $fieldsToHideProvider;
        $this->hideField = $hideField;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        return $this->modifyLevel($meta);
    }

    private function modifyLevel(array $meta): array
    {
        switch ($this->getCategoryLevel() <=> Subcategory::TOP_LEVEL) {
            case -1:
                $itemsToHide = $this->fieldsToHideProvider->getRootCategoryFields();
                $this->updateRootSwitcherConfig($meta['am_mega_menu_fieldset']['children']);
                break;
            case 0:
                $itemsToHide = $this->fieldsToHideProvider->getMainCategoryFields();
                break;
            case 1:
                $itemsToHide = $this->getSubcategoryFields();
                break;
        }
        $this->updateItemsToHide($itemsToHide);
        $meta = $this->hideFontConfig($meta);

        return $this->hideFields($meta, array_unique($itemsToHide));
    }

    private function hideFontConfig(array $meta): array
    {
        if ($this->entity->isObjectNew()) {
            $meta = $this->hideField->execute($meta, 'am_mega_menu_fieldset', ItemInterface::DESKTOP_FONT);
            $meta = $this->hideField->execute($meta, 'am_mega_menu_mobile_fieldset', ItemInterface::MOBILE_FONT);
        }

        return $meta;
    }

    private function getSubcategoryFields(): array
    {
        $parentCategory = $this->entity->getParentCategory();
        $level = (int) $this->entity->getLevel();
        $entityId = (int) $parentCategory->getEntityId();
        $storeId = (int) ($parentCategory->getStoreId() ?? Store::DEFAULT_STORE_ID);

        return $this->fieldsToHideProvider->getSubcategoryFields($level, $entityId, $storeId);
    }

    private function updateRootSwitcherConfig(?array &$meta): void
    {
        $meta[ItemInterface::HIDE_CONTENT]['arguments']['data']['config']['switcherConfig']['enabled'] = false;
    }

    private function getCategoryLevel(): int
    {
        if ($this->parentId && $this->entity->isObjectNew()) {
            $level = $this->entity->setParentId($this->parentId)->getParentCategory()->getLevel() + 1;
        } else {
            $level = $this->entity->getLevel();
        }

        return (int) $level;
    }

    private function updateItemsToHide(array &$itemsToHide)
    {
        if (!$this->entity->hasChildren()) {
            $itemsToHide[] = ItemInterface::SUBCATEGORIES_POSITION;
            $itemsToHide[] = ItemInterface::SUBMENU_TYPE;
        }

        if ($this->entity->isObjectNew()) {
            $itemsToHide[] = ItemInterface::SUBMENU_TYPE;
        }
    }

    private function hideFields(array $meta, array $fieldsToHide): array
    {
        foreach ($fieldsToHide as $field) {
            $meta = $this->hideField->execute($meta, 'am_mega_menu_fieldset', $field);
        }

        return $meta;
    }

    /**
     * @param Category $category
     * @return $this
     */
    public function setCategory($category)
    {
        $this->entity = $category;

        return $this;
    }

    public function isNeedCategory(): bool
    {
        return true;
    }
}
