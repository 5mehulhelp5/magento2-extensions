<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Ui\Component\Grid\Rule;

use Amasty\CommonRules\Model\MethodConverter;
use Amasty\CommonRules\Model\ResourceModel\Rule\Collection as CommonRulesCollection;
use Amasty\CommonRules\Ui\DataProvider\AbstractDataProvider;
use Amasty\Shiprules\Api\Data\RuleInterface;
use Magento\Framework\Api\Filter;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var PoolInterface
     */
    private $modifiersPool;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CommonRulesCollection $collection,
        MethodConverter $converter,
        array $meta = [],
        array $data = [],
        PoolInterface $modifiersPool = null
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $collection, $converter, $meta, $data);

        $this->modifiersPool = $modifiersPool;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $collection = $this->getCollection();
        $collection->joinRelationTables();

        $data = parent::getData();

        if (empty($data['totalRecords'])) {
            return $data;
        }

        foreach ($this->modifiersPool->getModifiersInstances() as $modifier) {
            $data = $modifier->modifyData($data);
        }

        return $data;
    }

    // During filtering by 'rule_id',
    // we need to indicate to which table we want to add filter,
    // to avoid exception "Column 'rule_id' in where clause is ambiguous".
    // Because column 'rule_id' exist in all relation tables.
    public function addFilter(Filter $filter): void
    {
        if ($filter->getField() === RuleInterface::RULE_ID) {
            $this->getCollection()->addFieldToFilter(
                'main_table.' . RuleInterface::RULE_ID,
                [$filter->getConditionType() => $filter->getValue()]
            );
        } else {
            parent::addFilter($filter);
        }
    }
}
