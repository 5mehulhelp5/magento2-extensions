<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Backend\Builder;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\UpdateDefaultValues;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\UpdateSortOrder as UpdateSortOrderResource;

class UpdateSortOrder
{
    /**
     * @var UpdateSortOrderResource
     */
    private $updateSortOrder;

    /**
     * @var GetItemCollection
     */
    private $getItemCollection;

    /**
     * @var UpdateDefaultValues
     */
    private $updateDefaultValues;

    public function __construct(
        UpdateSortOrderResource $updateSortOrder,
        GetItemCollection $getItemCollection,
        UpdateDefaultValues $updateDefaultValues
    ) {
        $this->updateSortOrder = $updateSortOrder;
        $this->getItemCollection = $getItemCollection;
        $this->updateDefaultValues = $updateDefaultValues;
    }

    public function execute(int $store): void
    {
        $items = [];
        foreach ($this->getItemCollection->execute()->getData() as $key => $item) {
            $items[] = [
                Position::ENTITY_ID => $item[Position::ENTITY_ID],
                Position::TYPE => $item[Position::TYPE],
                Position::STORE_VIEW => $store,
                Position::POSITION => $key
            ];
        }

        if (!empty($items)) {
            $this->updateSortOrder->execute($items);

            $items = array_filter(
                $items,
                function (array $items): bool {
                    return $items[Position::TYPE] === ItemInterface::CUSTOM_TYPE;
                }
            );
            $this->updateDefaultValues->execute($items);
        }
    }
}
