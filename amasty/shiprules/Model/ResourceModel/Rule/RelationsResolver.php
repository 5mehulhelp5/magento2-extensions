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
use Magento\Framework\App\ResourceConnection;

class RelationsResolver
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public const STORE_FIELD = 'store_id';
    public const CUSTOMER_GROUP_FIELD = 'customer_group';
    public const DAY_FIELD = 'day';

    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    public function resolve(array $data, RuleInterface $rule): void
    {
        $ruleId = (int)$rule->getRuleId();
        $this->saveStoreIds($data, $ruleId);
        $this->saveCustomerGroups($data, $ruleId);
        $this->saveDays($data, $ruleId);
    }

    private function saveStoreIds(array $data, int $ruleId): void
    {
        $this->deleteData(Rule::STORES_TABLE_NAME, $ruleId);

        if (isset($data[RuleInterface::STORES]) && is_array($data[RuleInterface::STORES])
            && !in_array('0', $data[RuleInterface::STORES], true)
        ) {
            $this->insertData($data[RuleInterface::STORES], self::STORE_FIELD, $ruleId, Rule::STORES_TABLE_NAME);
        }
    }

    private function saveCustomerGroups(array $data, int $ruleId): void
    {
        $this->deleteData(Rule::CUSTOMERS_TABLE_NAME, $ruleId);

        if (!empty($data[RuleInterface::CUST_GROUPS])) {
            $this->insertData(
                $data[RuleInterface::CUST_GROUPS],
                self::CUSTOMER_GROUP_FIELD,
                $ruleId,
                Rule::CUSTOMERS_TABLE_NAME
            );
        }
    }

    private function saveDays(array $data, int $ruleId): void
    {
        $this->deleteData(Rule::DAYS_TABLE_NAME, $ruleId);
        if (!empty($data[RuleInterface::DAYS])) {
            $this->insertData($data[RuleInterface::DAYS], self::DAY_FIELD, $ruleId, Rule::DAYS_TABLE_NAME);
        }
    }

    private function insertData(array $dataToInsert, string $fieldName, int $ruleId, string $tableName): void
    {
        if (empty($dataToInsert)) {
            return;
        }

        $connection = $this->resourceConnection->getConnection();
        $insertArray = [];
        foreach ($dataToInsert as $data) {
            $insertArray[] = [
                $ruleId,
                $data
            ];
        }

        $connection->insertArray(
            $this->resourceConnection->getTableName($tableName),
            [RuleInterface::RULE_ID, $fieldName],
            $insertArray
        );
    }

    private function deleteData(string $tableName, int $ruleId): void
    {
        $connection = $this->resourceConnection->getConnection();
        $connection->delete(
            $this->resourceConnection->getTableName($tableName),
            [RuleInterface::RULE_ID . ' = ?' => $ruleId]
        );
    }
}
