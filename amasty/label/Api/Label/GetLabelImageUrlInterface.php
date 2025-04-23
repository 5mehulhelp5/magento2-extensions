<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Api\Label;

use Amasty\Label\Api\Data\LabelInterface;

/**
 * @api
 */
interface GetLabelImageUrlInterface
{
    /**
     * @param string|null $imageName
     * @return string|null
     */
    public function execute(?string $imageName): ?string;

    /**
     * @param LabelInterface $label
     * @return string|null
     */
    public function getByLabel(LabelInterface $label): ?string;
}
