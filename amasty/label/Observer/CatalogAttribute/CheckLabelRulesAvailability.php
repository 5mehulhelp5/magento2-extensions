<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Observer\CatalogAttribute;

use Amasty\Label\Model\ResourceModel\Label as LabelsResource;
use Amasty\Label\Model\ResourceModel\Label\Collection;
use Amasty\Label\Model\ResourceModel\Label\CollectionFactory;
use Amasty\Label\Model\RuleFactory;
use Amasty\Label\Model\Rule;
use Amasty\Label\Model\Source\Status;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Rule\Model\Condition\Combine;
use Magento\Rule\Model\Condition\CombineFactory;

class CheckLabelRulesAvailability
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var LabelsResource
     */
    private $labelsResource;

    public function __construct(
        CollectionFactory $collectionFactory,
        ManagerInterface $messageManager,
        RuleFactory $ruleFactory,
        Json $serializer,
        LabelsResource $labelsResource
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->messageManager = $messageManager;
        $this->ruleFactory = $ruleFactory;
        $this->serializer = $serializer;
        $this->labelsResource = $labelsResource;
    }

    /**
     * Check rules that contains affected attribute
     * If rules were found they will be set to inactive
     */
    public function disableRulesWithAttribute(string $attributeCode): int
    {
        /* @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addAttributeInConditionFilter($attributeCode);
        /** @var Rule $ruleModel */
        $ruleModel = $this->ruleFactory->create();
        $disabledRulesCount = 0;
        foreach ($collection->getItems() as $label) {
            $label->setStatus(Status::INACTIVE);

            // $ruleModel is only for conditions processing. No need to create several rule models.
            $ruleModel->setConditions(null);
            $ruleModel->setConditionsSerialized($label->getConditionSerialized());
            $conditions = $ruleModel->getConditions();
            $this->removeAttributeFromConditions($conditions, $attributeCode);
            $label->setConditionSerialized($this->serializer->serialize($conditions->asArray()));

            $this->labelsResource->save($label);

            $disabledRulesCount++;
        }

        return $disabledRulesCount;
    }

    public function addMessageWithCountForAttribute(int $disabledRulesCount, string $attributeCode): void
    {
        if ($disabledRulesCount) {
            $this->messageManager->addWarningMessage(
                __(
                    '%1 Amasty Product Label Rules based on "%2" attribute have been disabled.',
                    $disabledRulesCount,
                    $attributeCode
                )
            );
        }
    }

    /**
     * Remove catalog attribute condition by attribute code from rule conditions to prevent error
     *
     * @param string $combine
     * @param string $attributeCode
     * @return void
     */
    private function removeAttributeFromConditions(Combine $combine, string $attributeCode): void
    {
        $conditions = $combine->getConditions();
        foreach ($conditions as $conditionId => $condition) {
            if ($condition instanceof Combine) {
                $this->removeAttributeFromConditions($condition, $attributeCode);
            }
            if (($condition instanceof \Magento\Rule\Model\Condition\Product\AbstractProduct)
                && $condition->getAttribute() === $attributeCode
            ) {
                unset($conditions[$conditionId]);
            }
        }
        $combine->setConditions($conditions);
    }
}
