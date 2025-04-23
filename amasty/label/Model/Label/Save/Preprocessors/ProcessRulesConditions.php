<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\Save\Preprocessors;

use Amasty\Base\Model\Serializer;
use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Model\Label\Save\DataPreprocessorInterface;
use Amasty\Label\Model\LabelRegistry;
use Amasty\Label\Model\Rule\Condition\Combine as AmastyCombine;
use Amasty\Label\Model\RuleFactory;
use Magento\CatalogRule\Model\Rule\Condition\Combine as MagentoCombine;

class ProcessRulesConditions implements DataPreprocessorInterface
{
    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var LabelRegistry
     */
    private $labelRegistry;

    public function __construct(
        RuleFactory $ruleFactory,
        Serializer $serializer,
        LabelRegistry $labelRegistry
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->serializer = $serializer;
        $this->labelRegistry = $labelRegistry;
    }

    public function process(array $data): array
    {
        if (isset($data['label_display_conditions']['rule']['conditions'])) {
            $condition = $data['label_display_conditions']['rule'];
            unset($data['label_display_conditions']);
            $rule = $this->ruleFactory->create();
            $rule->loadPost($condition);
            $data[LabelInterface::CONDITION_SERIALIZED] = $this->serializer->serialize(
                $this->replaceCombineRecursive($rule->getConditions()->asArray())
            );
        } else {
            $existingConditions = $this->labelRegistry->getCurrentLabel()->getConditionSerialized();
            $data[LabelInterface::CONDITION_SERIALIZED] = $existingConditions;
        }

        return $data;
    }

    /**
     * Replace MagentoCombine. Need for old created rules.
     * Correct rule type set in \Amasty\Label\Model\Rule\Condition\Combine.
     */
    private function replaceCombineRecursive(array $conditions): array
    {
        if (isset($conditions['type']) && isset($conditions['conditions'])) {
            if ($conditions['type'] == MagentoCombine::class) {
                $conditions['type'] = AmastyCombine::class;
            }

            foreach ($conditions['conditions'] as $key => $condition) {
                if (isset($condition['conditions'])) {
                    $conditions['conditions'][$key] = $this->replaceCombineRecursive($condition);
                }
            }
        }

        return $conditions;
    }
}
