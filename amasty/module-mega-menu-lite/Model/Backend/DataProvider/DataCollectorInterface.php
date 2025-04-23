<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Backend\DataProvider;

interface DataCollectorInterface
{
    public function execute(array $data, int $storeId, int $entityId): array;
}
