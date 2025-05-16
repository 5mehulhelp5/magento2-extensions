<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Ui\Component\Grid\Rule\Modifiers;

use Amasty\Shiprules\Api\Data\RuleInterface;
use Amasty\Shiprules\Model\ResourceModel\Rule;
use Amasty\Shiprules\Model\ResourceModel\Rule\RelationsResolver;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class ModifyStores implements ModifierInterface
{
    public function modifyData(array $data): array
    {
        $storesField = Rule::STORES_TABLE_NAME . '.' . RelationsResolver::STORE_FIELD;

        foreach ($data['items'] as &$item) {
            if (isset($item[$storesField])) {
                $item[RuleInterface::STORES] = $item[$storesField] ?: '0';
            } else {
                $item[RuleInterface::STORES] = '0';
            }
        }

        return $data;
    }

    public function modifyMeta(array $meta): array
    {
        return $meta;
    }
}
