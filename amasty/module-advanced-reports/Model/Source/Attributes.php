<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Reports Base for Magento 2
 */

namespace Amasty\Reports\Model\Source;

use Magento\Catalog\Model\Product;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\OptionSourceInterface;

class Attributes implements OptionSourceInterface
{
    public const ALLOWED_FRONTEND_TYPES = [
        'boolean',
        'select',
        'multiselect',
        'swatch_text',
        'swatch_visual'
    ];

    /**
     * @var CollectionFactory
     */
    private $eavCollection;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    public function __construct(
        CollectionFactory $eavCollection,
        ?EavConfig $eavConfig = null // TODO move to not optional
    ) {
        $this->eavCollection = $eavCollection;
        $this->eavConfig = $eavConfig ?? ObjectManager::getInstance()->get(EavConfig::class);
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->eavCollection->create();
        $collection->addFieldToFilter(
            Set::KEY_ENTITY_TYPE_ID,
            $this->eavConfig->getEntityType(Product::ENTITY)->getEntityTypeId()
        );
        $collection->addFieldToFilter(AttributeInterface::FRONTEND_INPUT, ['in' => self::ALLOWED_FRONTEND_TYPES]);

        $allAttributes = $collection->load()->getItems();

        $attributes = [[
            'label' => __('Please Select...'),
            'value' => ''
        ]];
        foreach ($allAttributes as $attribute) {
            $attributes[] = [
                'value' => $attribute->getAttributeCode(),
                'label' => $attribute->getFrontendLabel()
            ];
        }

        return $attributes;
    }
}
