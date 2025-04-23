<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\Save;

interface DataPreprocessorInterface
{
    /**
     * Prepare and validate data before save
     *
     * @param array $data
     * @return array
     */
    public function process(array $data): array;
}
