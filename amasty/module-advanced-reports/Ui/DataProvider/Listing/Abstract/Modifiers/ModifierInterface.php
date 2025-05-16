<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Reports Base for Magento 2
 */

namespace Amasty\Reports\Ui\DataProvider\Listing\Abstract\Modifiers;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

interface ModifierInterface
{
    public function modifyData(array $data, AbstractCollection $collection): array;
}
