<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Backend\Builder;

use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position;

class UpdatePositionData
{
    /**
     * @var Position
     */
    private $positionResource;

    /**
     * @var UpdateSortOrder
     */
    private $updateSortOrder;

    public function __construct(
        Position $positionResource,
        UpdateSortOrder $updateSortOrder
    ) {
        $this->positionResource = $positionResource;
        $this->updateSortOrder = $updateSortOrder;
    }

    public function execute(int $storeId): void
    {
        $this->positionResource->importCategoryPositions($storeId);
        $this->updateSortOrder->execute($storeId);
    }
}
