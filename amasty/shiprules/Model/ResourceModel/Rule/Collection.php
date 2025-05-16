<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\ResourceModel\Rule;

use Amasty\Shiprules\Api\Data\RuleInterface;
use Amasty\Shiprules\Model\ResourceModel\Rule;
use Magento\Framework\DB\Select;

/**
 * Rules Collection
 */
class Collection extends \Amasty\CommonRules\Model\ResourceModel\Rule\Collection
{
    protected function _construct()
    {
        $this->_init(\Amasty\Shiprules\Model\Rule::class, Rule::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * @param int[] $storeIds
     * @param bool $withAll
     */
    public function addStoreFilter($storeIds, $withAll = true): Collection
    {
        $condition = [];
        $field = [];

        if ($storeIds && !is_array($storeIds)) {
            $storeIds = [$storeIds];
        }

        if ($withAll) {
            $condition[] = ['null' => true];
            $field[] = Rule::STORES_TABLE_NAME . '.' . RelationsResolver::STORE_FIELD;
        }

        if ($storeIds) {
            foreach ($storeIds as $storeId) {
                $condition[] = ['eq' => $storeId];
                $field[] = Rule::STORES_TABLE_NAME . '.' . RelationsResolver::STORE_FIELD;
            }
        }

        if (empty($field)) {
            return $this;
        }

        $this->joinTable(Rule::STORES_TABLE_NAME);
        $this->addFieldToFilter($field, $condition);
        $this->getSelect()->group('main_table.' . RuleInterface::RULE_ID);

        return $this;
    }

    /**
     * @param int $groupId
     */
    public function addCustomerGroupFilter($groupId): Collection
    {
        $this->joinTable(Rule::CUSTOMERS_TABLE_NAME);
        $this->addFieldToFilter(
            [
                Rule::CUSTOMERS_TABLE_NAME . '.' . RelationsResolver::CUSTOMER_GROUP_FIELD,
                Rule::CUSTOMERS_TABLE_NAME . '.' . RelationsResolver::CUSTOMER_GROUP_FIELD
            ],
            [['null' => true], ['eq' => $groupId]]
        );

        return $this;
    }

    public function addDaysFilter(): Collection
    {
        $this->joinTable(Rule::DAYS_TABLE_NAME);
        $localeDate = $this->localeDate->date();

        $this->addFieldToFilter(
            [
                Rule::DAYS_TABLE_NAME . '.' . RelationsResolver::DAY_FIELD,
                Rule::DAYS_TABLE_NAME . '.' . RelationsResolver::DAY_FIELD
            ],
            [['null' => true], ['eq' => $localeDate->format('N')]]
        );

        //current time + 1 min. Format: hours + minutes without any delimiters
        $timeToFilter = $localeDate->format('H') * 100 + $localeDate->format('i') + 1;

        $this->getSelect()->where('(time_from IS NULL) OR (time_to IS NULL)
        OR time_from="0" OR time_to="0" OR
        (time_from < ' . $timeToFilter . ' AND time_to > ' . $timeToFilter . ') OR
        (time_from < ' . $timeToFilter . ' AND time_to < time_from) OR
        (time_to > ' . $timeToFilter . ' AND time_to < time_from)');

        return $this;
    }

    public function joinRelationTables(): void
    {
        $this->joinTable(Rule::STORES_TABLE_NAME, RelationsResolver::STORE_FIELD);
        $this->joinTable(Rule::CUSTOMERS_TABLE_NAME, RelationsResolver::CUSTOMER_GROUP_FIELD);
        $this->joinTable(Rule::DAYS_TABLE_NAME, RelationsResolver::DAY_FIELD);

        $this->getSelect()->group('main_table.' . RuleInterface::RULE_ID);
    }

    private function joinTable(string $tableName, string $fieldName = ''): void
    {
        $fieldToSelect = [];
        if ($fieldName) {
            $fieldToSelect = [$tableName . '.' . $fieldName
            => 'GROUP_CONCAT(DISTINCT ' . $tableName . '.' . $fieldName . ' SEPARATOR ",")'];
        }
        $select = $this->getSelect();
        if (!array_key_exists($tableName, $select->getPart(Select::FROM))) {
            $select->joinLeft(
                [$tableName => $this->getTable($tableName)],
                'main_table.' . RuleInterface::RULE_ID. ' = ' . $tableName . '.' . RuleInterface::RULE_ID,
                $fieldToSelect
            );
        }
    }
}
