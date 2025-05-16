<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Notification\DefaultAlert;

use Magento\Customer\Api\Data\CustomerInterface;

class CustomerRegistry
{
    /**
     * @var CustomerInterface
     */
    private $customer;

    public function register(CustomerInterface $customer): void
    {
        $this->customer = $customer;
    }

    public function get(): ?CustomerInterface
    {
        return $this->customer;
    }

    public function clear(): void
    {
        $this->customer = null;
    }
}
