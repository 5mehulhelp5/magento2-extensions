<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class AddHideAlertAttribute implements DataPatchInterface, PatchRevertableInterface
{
    private const ATTRIBUTE_NAME = 'amxnotif_hide_alert';

    /**
     * @var EavSetup
     */
    private $eavSetup;

    public function __construct(EavSetupFactory $eavSetupFactory, ModuleDataSetupInterface $setup)
    {
        $this->eavSetup = $eavSetupFactory->create(['setup' => $setup]);
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function apply(): self
    {
        if ($this->isCanApply()) {
            $this->eavSetup->addAttribute(
                Product::ENTITY,
                self::ATTRIBUTE_NAME,
                [
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Hide Stock Alert Block',
                    'input' => 'boolean',
                    'used_in_product_listing'   => true,
                    'class' => '',
                    'source' => Boolean::class,
                    'global' => Attribute::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '0',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'unique' => false,
                    'apply_to' => ''
                ]
            );

            $this->addToAttributeSet();
        }

        return $this;
    }

    public function getAliases(): array
    {
        return [];
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function revert(): void
    {
        $this->eavSetup->removeAttribute(Product::ENTITY, self::ATTRIBUTE_NAME);
    }

    private function isCanApply(): bool
    {
        return !$this->eavSetup->getAttribute(Product::ENTITY, self::ATTRIBUTE_NAME);
    }

    private function addToAttributeSet(): void
    {
        $attributeId = $this->eavSetup->getAttributeId(
            Product::ENTITY,
            self::ATTRIBUTE_NAME
        );

        $attributeSets = $this->eavSetup->getAllAttributeSetIds(
            Product::ENTITY
        );

        foreach ($attributeSets as $attributeSetId) {
            try {
                $attributeGroupId = $this->eavSetup->getAttributeGroupId(
                    Product::ENTITY,
                    $attributeSetId,
                    'General'
                );
            } catch (\Exception $e) {
                $attributeGroupId = $this->eavSetup->getDefaultAttributeGroupId(
                    Product::ENTITY,
                    $attributeSetId
                );
            }

            $this->eavSetup->addAttributeToSet(
                Product::ENTITY,
                $attributeSetId,
                $attributeGroupId,
                $attributeId
            );
        }
    }
}
