<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Ui\DataProvider\Form\Category\Modifier;

use Amasty\MegaMenuLite\Model\Menu\Content\Resolver;
use Magento\Catalog\Model\Category;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\PageBuilder\Model\Config;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class PageBuilder implements ModifierInterface
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->moduleManager = $moduleManager;
        $this->scopeConfig = $scopeConfig;
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
        return $this->modifyPageBuilder($meta);
    }

    private function modifyPageBuilder(array $meta): array
    {
        $config = &$meta['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config'];
        if ($this->isPageBuilderEnabled()) {
            if ($this->moduleManager->isEnabled('Amasty_MegaMenuPageBuilder')) {
                $config['default'] = Resolver::CHILD_CATEGORIES_PAGE_BUILDER;
                $config['notice'] = __('You can use the menu item Add Content for showing child categories.');
                $config['defaultNotice'] = $config['notice'];
            } else {
                $config['default'] = Resolver::CHILD_CATEGORIES;
            }
            $config['component'] = 'Amasty_MegaMenuLite/js/form/components/wysiwyg';
        } else {
            $config['default'] = Resolver::CHILD_CATEGORIES;
            $config['component'] = 'Amasty_MegaMenuLite/js/form/element/wysiwyg';
            $config['notice'] = __(
                'You can use the variable: {{child_categories_content}} for showing child categories.'
            );
            $config['defaultNotice'] = $config['notice'];
        }

        return $meta;
    }

    public function isNeedCategory(): bool
    {
        return false;
    }

    private function isPageBuilderEnabled(): bool
    {
        return $this->moduleManager->isEnabled('Magento_PageBuilder')
            && $this->scopeConfig->getValue('cms/pagebuilder/enabled', ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }
}
