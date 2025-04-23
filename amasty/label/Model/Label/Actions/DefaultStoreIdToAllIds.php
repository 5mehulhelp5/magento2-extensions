<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\Actions;

use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class DefaultStoreIdToAllIds
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

    /**
     * @param array $relatedEntityIds
     * @return array
     */
    public function execute(array $relatedEntityIds): array
    {
        if (in_array(Store::DEFAULT_STORE_ID, $relatedEntityIds)) {
            $relatedEntityIds = array_map(function (StoreInterface $store): int {
                return (int) $store->getId();
            }, $this->storeManager->getStores(false));
        }

        return $relatedEntityIds;
    }
}
