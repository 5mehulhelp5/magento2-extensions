<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Notification\DefaultAlert\Factory;

use Magento\Framework\Data\Collection;

interface AlertFactoryInterface
{
    public function create(): Collection;
}
