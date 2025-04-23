<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Ui\DataProvider\Form\Link\Modifier;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenu\Model\Backend\Ui\HideMobileFieldset;
use Amasty\MegaMenuLite\Model\Backend\DataProvider\LinkRegistry;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class Level implements ModifierInterface
{
    /**
     * @var LinkInterface
     */
    private $entity;

    /**
     * @var int|null
     */
    private $storeId;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var HideMobileFieldset
     */
    private $hideMobileFieldset;

    public function __construct(
        LinkRegistry $linkRegistry,
        ArrayManager $arrayManager,
        HideMobileFieldset $hideMobileFieldset
    ) {
        $this->entity = $linkRegistry->getLink();
        $this->storeId = $linkRegistry->getStoreId();
        $this->arrayManager = $arrayManager;
        $this->hideMobileFieldset = $hideMobileFieldset;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        return $this->modifyLevel($meta);
    }

    private function modifyLevel(array $meta): array
    {
        if ($this->entity->getLevel() !== LinkInterface::DEFAULT_LEVEL || $this->entity->isObjectNew()) {
            $meta = $this->hideFontConfig($meta);
            $meta = $this->hideMobileFieldset->execute($meta);
        }

        return $meta;
    }

    private function hideFontConfig(array $meta): array
    {
        $meta = $this->hideField($meta, 'am_mega_menu_fieldset', ItemInterface::DESKTOP_FONT);
        $meta = $this->hideField($meta, 'am_mega_menu_mobile_fieldset', ItemInterface::MOBILE_FONT);

        return $meta;
    }

    private function hideField(array $meta, string $fieldSet, string $field): array
    {
        $path = sprintf(
            '%s/%s/%s/%s',
            $fieldSet,
            'children',
            $field,
            'arguments/data/config/visible'
        );
        $meta = $this->arrayManager->set($path, $meta, false);

        $path = sprintf(
            '%s/%s/%s/%s',
            $fieldSet,
            'children',
            $field,
            'arguments/data/config/hidden'
        );
        $meta = $this->arrayManager->set($path, $meta, true);

        return $meta;
    }
}
