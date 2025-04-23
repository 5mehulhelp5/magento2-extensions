<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Premium Base for Magento 2
 */

namespace Amasty\MegaMenuPremium\Ui\DataProvider\Form\Category\Modifier;

use Amasty\MegaMenu\Model\ConfigProvider;
use Amasty\MegaMenu\Model\Menu\Subcategory;
use Amasty\MegaMenu\Model\OptionSource\MobileTemplate;
use Amasty\MegaMenuPremium\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuPremium\Model\DataProvider\GetParentIdFromRequest;
use Magento\Catalog\Model\Category;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class HideItems implements ModifierInterface
{
    /**
     * @var Category
     */
    private $entity;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var GetParentIdFromRequest
     */
    private $getParentId;

    public function __construct(
        ConfigProvider $configProvider,
        GetParentIdFromRequest $getParentId
    ) {
        $this->configProvider = $configProvider;
        $this->getParentId = $getParentId;
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
                $meta = $this->modifyRootCategory($meta);
                break;
            case 0:
                $meta = $this->modifyMainCategory($meta);
                break;
            case 1:
                $meta = $this->modifyChildCategory($meta);
                break;
        }

        return $meta;
    }

    private function modifyRootCategory(array $meta): array
    {
        $meta['am_mega_menu_mobile_fieldset']['arguments']['data']['config']['visible'] = false;
        $field = ItemInterface::SUBMENU_ANIMATION;
        $meta['am_mega_menu_fieldset']['children'][$field]['arguments']['data']['config']['visible'] = false;
        $meta['am_mega_menu_fieldset']['children'][$field]['arguments']['data']['config']['hidden'] = true;

        return $meta;
    }

    private function modifyMainCategory(array $meta): array
    {
        return $meta;
    }

    private function modifyChildCategory(array $meta): array
    {
        if ($this->configProvider->getMobileTemplateClass() == MobileTemplate::ACCORDION) {
            $meta['am_mega_menu_mobile_fieldset']['arguments']['data']['config']['visible'] = false;
        }
        $field = ItemInterface::SUBMENU_ANIMATION;
        $meta['am_mega_menu_fieldset']['children'][$field]['arguments']['data']['config']['visible'] = false;
        $meta['am_mega_menu_fieldset']['children'][$field]['arguments']['data']['config']['hidden'] = true;

        return $meta;
    }

    private function getCategoryLevel(): int
    {
        $parentId = $this->getParentId->execute();
        if ($parentId && $this->entity->isObjectNew()) {
            $level = $this->entity->setParentId($parentId)->getParentCategory()->getLevel() + 1;
        } else {
            $level = $this->entity->getLevel();
        }

        return (int) $level;
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
