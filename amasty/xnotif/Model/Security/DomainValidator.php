<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Security;

use Magento\Store\Model\StoreManagerInterface;

class DomainValidator
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    public function isValid(string $url): bool
    {
        return $this->getHost() === $this->getHost($url);
    }

    private function getHost(?string $url = null): string
    {
        // phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
        return parse_url($url ?? $this->storeManager->getStore()->getBaseUrl(), PHP_URL_HOST);
    }
}
