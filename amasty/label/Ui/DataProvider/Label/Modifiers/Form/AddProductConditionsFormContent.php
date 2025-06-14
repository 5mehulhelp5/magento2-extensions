<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Ui\DataProvider\Label\Modifiers\Form;

use Amasty\Base\Model\Serializer;
use Amasty\Label\Model\LabelRegistry;
use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Model\Rule;
use Amasty\Label\Model\RuleFactory;
use Magento\Framework\UrlInterface;
use Magento\Rule\Model\Condition\Combine;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class AddProductConditionsFormContent implements ModifierInterface
{
    public const CONDITIONS_ID = 'rule_conditions_fieldset';
    public const FORM_NAME = 'amasty_labels_conditions';

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var LabelRegistry
     */
    private $labelRegistry;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        RuleFactory $ruleFactory,
        LabelRegistry $labelRegistry,
        UrlInterface $urlBuilder,
        Serializer $serializer
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->labelRegistry = $labelRegistry;
        $this->urlBuilder = $urlBuilder;
    }

    public function modifyData(array $data): array
    {
        return $data;
    }

    public function modifyMeta(array $meta): array
    {
        $preparedMeta = [
            'formContent' => $this->prepareRuleModel()->getConditions()->asHtmlRecursive(),
            'newFormChildUrl' => $this->getNewFormChildUrl(),
            'conditionsFormId' => self::CONDITIONS_ID,
        ];

        $meta['product_conditions']['children']['label_display_conditions']
        ['arguments']['data']['config'] = $preparedMeta;

        return $meta;
    }

    private function getNewFormChildUrl(): string
    {
        return $this->urlBuilder->getUrl(
            'amasty_label/label/edit_newConditionHtml',
            ['form_namespace' => self::FORM_NAME]
        );
    }

    private function getConditionSerialized(): ?string
    {
        $label = $this->labelRegistry->getCurrentLabel();
        $result = null;

        if ($label !== null) {
            $result = $label->getData(LabelInterface::CONDITION_SERIALIZED);
        }

        return $result;
    }

    private function prepareRuleModel(): Rule
    {
        $ruleModel = $this->ruleFactory->create();
        $ruleModel->setConditions([]);
        $ruleModel->setConditionsSerialized($this->getConditionSerialized());
        $ruleModel->getConditions()->setJsFormObject(self::CONDITIONS_ID);
        $ruleModel->getConditions()->setFormName(self::FORM_NAME);
        $this->setFormNameFromChildCondition($ruleModel->getConditions()->getConditions());

        return $ruleModel;
    }

    private function setFormNameFromChildCondition(?array $conditions): void
    {
        /** @var Combine $condition */
        foreach ($conditions ?? [] as $condition) {
            $condition->setFormName(self::FORM_NAME);
            $this->setFormNameFromChildCondition($condition->getConditions());
        }
    }
}
