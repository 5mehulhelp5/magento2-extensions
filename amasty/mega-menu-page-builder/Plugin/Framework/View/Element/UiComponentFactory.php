<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Mega Menu PageBuilder for Magento 2 (System)
 */

namespace Amasty\MegaMenuPageBuilder\Plugin\Framework\View\Element;

use Amasty\MegaMenu\Model\Menu\Subcategory;
use Magento\Catalog\Model\Category;
use Magento\Framework\View\Element\UiComponentFactory as NativeUiComponentFactory;
use Magento\PageBuilder\Component\Form\Element\Wysiwyg;
use Magento\Store\Model\Store;

class UiComponentFactory
{
    private const IGNORE_WYSIWYG_IDS = [
        'category_form_content',
        'catalogstaging_category_update_form_content'
    ];

    /**
     * @var Subcategory
     */
    private $subcategory;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    public function __construct(
        \Magento\Framework\Registry $registry,
        Subcategory $subcategory
    ) {
        $this->subcategory = $subcategory;
        $this->registry = $registry;
    }

    /**
     * @param NativeUiComponentFactory $subject
     * @param $result
     *
     * @return mixed
     */
    public function afterCreate(NativeUiComponentFactory $subject, $result)
    {
        if ($result instanceof Wysiwyg) {
            $config = $result->getData('config');
            if (isset($config['wysiwygConfigData']['content_types']['ammega_menu_widget'])
                && ($this->checkWysiwygId($config) || $this->isShowMegaMenu())
            ) {
                unset($config['wysiwygConfigData']['content_types']['ammega_menu_widget']);
                $result->setData('config', $config);
            }
        }

        return $result;
    }

    private function checkWysiwygId(array $config): bool
    {
        return !isset($config['wysiwygId'])
            || !in_array($config['wysiwygId'], self::IGNORE_WYSIWYG_IDS, true);
    }

    private function isShowMegaMenu(): bool
    {
        /** @var Category $category */
        $category = $this->registry->registry('category');
        if (!$category) {
            return false;
        }
        $parentCategory = $this->getParentCategory($category, Subcategory::TOP_LEVEL);

        return $parentCategory && $this->subcategory->isShowSubcategories(
            (int) $parentCategory->getLevel(),
            (int) $parentCategory->getEntityId(),
            (int) ($parentCategory->getStoreId() ?? Store::DEFAULT_STORE_ID)
        );
    }

    private function getParentCategory($category, int $level)
    {
        if ($category->getLevel() > $level) {
            $category = $this->getParentCategory($category->getParentCategory(), $level);
        }

        return $category;
    }
}
