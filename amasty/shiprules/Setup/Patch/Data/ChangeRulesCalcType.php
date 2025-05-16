<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Setup\Patch\Data;

use Amasty\CommonRules\Model\OptionProvider\Provider\CalculationOptionProvider;
use Amasty\Shiprules\Api\Data\RuleInterface;
use Amasty\Shiprules\Model\ResourceModel\Rule as RuleResource;
use Amasty\Shiprules\Model\ResourceModel\Rule\Collection;
use Amasty\Shiprules\Model\ResourceModel\Rule\CollectionFactory;
use Amasty\Shiprules\Model\Rule;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class ChangeRulesCalcType implements DataPatchInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var RuleResource
     */
    private $resource;

    /**
     * @var State
     */
    private $appState;

    public function __construct(
        CollectionFactory $collectionFactory,
        RuleResource $resource,
        State $appState
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->resource = $resource;
        $this->appState = $appState;
    }

    /**
     * Change calculation type if rule is 'Partial Replace' and has empty product conditions
     *
     * @return void
     */
    public function apply(): void
    {
        $this->appState->emulateAreaCode(Area::AREA_ADMINHTML, [$this, 'changeCalcType']);
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Change calculation type
     */
    public function changeCalcType(): void
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(
            RuleInterface::CALC,
            CalculationOptionProvider::CALC_REPLACE_PRODUCT
        );

        /** @var Rule $rule */
        foreach ($collection->getItems() as $rule) {
            if (empty($rule->getActions()->getActions())) {
                $rule->setCalc(CalculationOptionProvider::CALC_REPLACE);
                $this->resource->save($rule);
            }
        }
    }
}
