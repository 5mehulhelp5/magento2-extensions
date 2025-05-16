<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Setup\Patch\Data;

use Amasty\Base\Setup\SerializedFieldDataConverter;
use Amasty\Shiprules\Model\ResourceModel\Rule;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class ConvertConditionsToJson implements DataPatchInterface
{
    /**
     * @var SerializedFieldDataConverter
     */
    private $fieldDataConverter;

    public function __construct(SerializedFieldDataConverter $fieldDataConverter)
    {
        $this->fieldDataConverter = $fieldDataConverter;
    }

    public function apply(): void
    {
        $fields = ['conditions_serialized', 'actions_serialized'];
        $this->fieldDataConverter->convertSerializedDataToJson(Rule::TABLE_NAME, 'rule_id', $fields);
    }

    public function getAliases(): array
    {
        return [];
    }

    public static function getDependencies(): array
    {
        return [];
    }
}
