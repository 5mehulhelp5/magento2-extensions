<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Observer\CatalogAttribute;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * event catalog_entity_attribute_save_after
 * observer name Amasty_Label::DisableAttributeRules
 */
class SaveAfterObserver implements ObserverInterface
{
    /**
     * @var CheckLabelRulesAvailability
     */
    private $rulesChecker;

    public function __construct(
        CheckLabelRulesAvailability $rulesChecker
    ) {
        $this->rulesChecker = $rulesChecker;
    }

    /**
     * After save attribute if it is not used for promo rules already check rules for containing this attribute
     */
    public function execute(EventObserver $observer): void
    {
        /** @var Attribute $attribute */
        $attribute = $observer->getEvent()->getData('attribute');
        if ($attribute->dataHasChangedFor('is_used_for_promo_rules') && !$attribute->getIsUsedForPromoRules()) {
            $attributeCode = $attribute->getAttributeCode();
            $count = $this->rulesChecker->disableRulesWithAttribute($attributeCode);
            $this->rulesChecker->addMessageWithCountForAttribute($count, $attributeCode);
        }
    }
}
