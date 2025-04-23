<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Form Base for Magento 2
 */

namespace Amasty\Customform\Model\Form\Save\Preparation;

use Amasty\Customform\Api\Data\FormInterface;
use Magento\Store\Model\Store;

class PrepareStoreIds implements PreparationInterface
{
    public function prepare(array $formData): array
    {
        $storeIds = $formData[FormInterface::STORE_ID] ?? [Store::DEFAULT_STORE_ID];
        $formData[FormInterface::STORE_ID] = join(',', $storeIds);

        return $formData;
    }
}
