<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Ui\DataProvider\Label\Modifiers\Form;

use Amasty\Label\Model\LabelRegistry;
use Amasty\Label\Model\ResourceModel\Label\GetRelatedEntitiesIds as GetCustomerGroupIds;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class AddCustomerGroupsData implements ModifierInterface
{
    use LabelSpecificSettingsTrait;

    public const DATA_SCOPE = 'customer_group_ids';

    /**
     * @var GetCustomerGroupIds
     */
    private $getCustomerGroupsIds;

    /**
     * @var LabelRegistry
     */
    private $labelRegistry;

    public function __construct(
        GetCustomerGroupIds $getCustomerGroupsIds,
        LabelRegistry $labelRegistry
    ) {
        $this->getCustomerGroupsIds = $getCustomerGroupsIds;
        $this->labelRegistry = $labelRegistry;
    }

    protected function executeIfLabelExists(int $labelId, array $data): array
    {
        $customerGroups = $this->getCustomerGroupsIds->execute($labelId);
        $labelData = $data[$labelId] ?? [];
        $labelData[self::DATA_SCOPE] = join(',', $customerGroups);
        $data[$labelId] = $labelData;

        return $data;
    }
}
