<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Setup\Patch\Data;

use Amasty\Shiprules\Model\ResourceModel\Rule as RuleResource;
use Amasty\Shiprules\Model\ResourceModel\Rule\Collection;
use Amasty\Shiprules\Model\ResourceModel\Rule\CollectionFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class MigrateDataToSeparateTables implements DataPatchInterface
{
    /**
     * @var RuleResource
     */
    private $resource;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        RuleResource $resource,
        CollectionFactory $collectionFactory
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
    }

    public function apply(): void
    {
        /** @var Collection $colelction */
        $collection = $this->collectionFactory->create();
        $storeInsert = [];
        $groupsInsert = [];
        $daysInsert = [];
        foreach ($collection->getData() as $ruleData) {
            $ruleId = (int)$ruleData['rule_id'];

            if ($ruleData['stores']) {
                $stores = explode(',', $ruleData['stores']);
                if (!in_array(0, $stores, false)) {
                    foreach ($stores as $storeId) {
                        $storeInsert[] = [
                            $ruleId,
                            (int)$storeId
                        ];
                    }
                }
            }

            if ($ruleData['cust_groups'] || $ruleData['cust_groups'] === '0') {
                foreach (explode(',', $ruleData['cust_groups']) as $group) {
                    $groupsInsert[] = [
                        $ruleId,
                        (int)$group
                    ];
                }
            }

            if ($ruleData['days']) {
                foreach (explode(',', $ruleData['days']) as $day) {
                    $daysInsert[] = [
                        $ruleId,
                        (int)$day
                    ];
                }
            }
        }
        $adapter = $this->resource->getConnection();
        if ($storeInsert) {
            $adapter->insertArray(
                $this->resource->getTable('amasty_shiprules_rule_stores'),
                ['rule_id', 'store_id'],
                $storeInsert
            );
        }
        if ($groupsInsert) {
            $adapter->insertArray(
                $this->resource->getTable('amasty_shiprules_rule_customer_groups'),
                ['rule_id', 'customer_group'],
                $groupsInsert
            );
        }
        if ($daysInsert) {
            $adapter->insertArray(
                $this->resource->getTable('amasty_shiprules_rule_days'),
                ['rule_id', 'day'],
                $daysInsert
            );
        }

        $adapter->update($this->resource->getMainTable(), ['stores' => null, 'cust_groups' => null, 'days' => null]);
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
}
