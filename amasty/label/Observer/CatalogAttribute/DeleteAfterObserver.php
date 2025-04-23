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
 * event catalog_entity_attribute_delete_after
 * observer name Amasty_Label::DisableAttributeRules
 */
class DeleteAfterObserver implements ObserverInterface
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
     * After delete attribute check rules that contains deleted attribute
     * If rules was found they will set to inactive and added notice to admin session
     */
    public function execute(EventObserver $observer): void
    {
        /** @var Attribute $attribute */
        $attribute = $observer->getEvent()->getData('attribute');
        if ($attribute->getIsUsedForPromoRules()) {
            $attributeCode = $attribute->getAttributeCode();
            $count = $this->rulesChecker->disableRulesWithAttribute($attributeCode);
            $this->rulesChecker->addMessageWithCountForAttribute($count, $attributeCode);
        }
    }
}
