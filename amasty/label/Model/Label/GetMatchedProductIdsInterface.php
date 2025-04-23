<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label;

use Amasty\Label\Api\Data\LabelInterface;

interface GetMatchedProductIdsInterface
{
    /**
     * @param LabelInterface $label
     * @param array|null $productIds
     *
     * @return int[]
     */
    public function execute(LabelInterface $label, ?array $productIds): array;
}
