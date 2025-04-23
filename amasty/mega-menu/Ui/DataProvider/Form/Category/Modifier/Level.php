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
use Amasty\MegaMenu\Model\Backend\Ui\HideMobileFieldset;
use Amasty\MegaMenu\Model\Menu\Subcategory;
use Amasty\MegaMenu\Model\OptionSource\SubcategoriesPosition;
use Amasty\MegaMenu\Model\Provider\FieldsToHideProvider;
use Amasty\MegaMenuLite\Model\Provider\FieldsByStore;
use Magento\Catalog\Model\Category;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\Store;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class Level implements ModifierInterface
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
     * @var SubcategoriesPosition
     */
    private $subcategoriesPosition;

    /**
     * @var Subcategory
     */
    private $subcategory;

    /**
     * @var FieldsByStore
     */
    private $fieldsByStore;

    /**
     * @var HideField
     */
    private $hideField;

    /**
     * @var HideMobileFieldset
     */
    private $hideMobileFieldset;

    public function __construct(
        RequestInterface $request,
        SubcategoriesPosition $subcategoriesPosition,
        Subcategory $subcategory,
        FieldsByStore $fieldsByStore,
        HideField $hideField,
        HideMobileFieldset $hideMobileFieldset
    ) {
        $this->parentId = (int) $request->getParam('parent', 0);
        $this->subcategoriesPosition = $subcategoriesPosition;
        $this->subcategory = $subcategory;
        $this->fieldsByStore = $fieldsByStore;
        $this->hideField = $hideField;
        $this->hideMobileFieldset = $hideMobileFieldset;
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
        $level = $this->getCategoryLevel();
        switch ($level <=> Subcategory::TOP_LEVEL) {
            case -1:
                $meta = $this->getRootCategoryMeta($meta);
                break;
            case 0:
                $meta = $this->getMainCategoryMeta($meta);
                break;
            case 1:
                $meta = $this->getSubcategoryMeta($meta);
                break;
        }
        $this->setLevel($meta['am_mega_menu_fieldset']['children'], $level);

        return $meta;
    }

    private function setLevel(?array &$meta, int $level): void
    {
        foreach ($this->fieldsByStore->getCategoryFields()['am_mega_menu_fieldset'] as $field) {
            $meta[$field]['arguments']['data']['config']['level'] = $level;
        }
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

    private function getRootCategoryMeta(array $meta): array
    {
        $options = $this->subcategoriesPosition->toOptionArray();
        $switcherConfig = false;
        $meta = $this->hideFontConfig($meta);

        return $this->updateMeta($meta, $switcherConfig, $options);
    }

    private function getMainCategoryMeta(array $meta): array
    {
        $options = $this->subcategoriesPosition->toOptionArray();
        $switcherConfig = true;
        $level = (int) $this->entity->getLevel();
        $entityId = (int) $this->entity->getEntityId();
        $storeId = $this->entity->getStoreId() ?? Store::DEFAULT_STORE_ID;
        if ($this->subcategory->isShowSubcategories($level, $entityId, $storeId)) {
            $meta = $this->unsetContentNotice($meta);
        }
        $meta = $this->hideMobileError($meta);

        return $this->updateMeta($meta, $switcherConfig, $options);
    }

    private function getSubcategoryMeta(array $meta): array
    {
        $options = $this->subcategoriesPosition->toOptionArray(true);
        $switcherConfig = false;

        $parentCategory = $this->entity->getParentCategory();
        $level = (int) $parentCategory->getLevel();
        $entityId = (int) $parentCategory->getEntityId();
        $storeId = $parentCategory->getStoreId() ?? Store::DEFAULT_STORE_ID;
        if ($parentCategory && $this->subcategory->isShowSubcategories($level, $entityId, $storeId)) {
            $meta = $this->unsetContentNotice($meta);
        }
        $meta = $this->hideFontConfig($meta);
        $meta = $this->hideMobileError($meta);
        $meta = $this->hideMobileFieldset->execute($meta);

        return $this->updateMeta($meta, $switcherConfig, $options);
    }

    private function hideMobileError(array $meta): array
    {
        return $this->hideField->execute(
            $meta,
            'am_mega_menu_mobile_fieldset',
            FieldsToHideProvider::CATEGORY_LEVEL_ERROR
        );
    }

    private function unsetContentNotice(array $meta): array
    {
        unset(
            $meta['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config']['notice']
        );
        unset(
            $meta['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config']['default']
        );

        return $meta;
    }

    private function hideFontConfig(array $meta): array
    {
        $meta = $this->hideField->execute($meta, 'am_mega_menu_fieldset', ItemInterface::DESKTOP_FONT);
        $meta = $this->hideField->execute($meta, 'am_mega_menu_mobile_fieldset', ItemInterface::MOBILE_FONT);

        return $meta;
    }

    private function updateMeta(array $meta, bool $switcherConfig, array $options): array
    {
        $fields = &$meta['am_mega_menu_fieldset']['children'];
        $fields['submenu_type']['arguments']['data']['config']['switcherConfig']['enabled'] = $switcherConfig;
        $fields['subcategories_position']['arguments']['data']['options'] = $options;

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
