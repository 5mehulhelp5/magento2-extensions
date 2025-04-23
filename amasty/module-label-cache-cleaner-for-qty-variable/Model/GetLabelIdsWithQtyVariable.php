<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Label Cache Cleaner for Qty Variable
 */

namespace Amasty\LabelCacheCleanerForQtyVariable\Model;

use Amasty\LabelCacheCleanerForQtyVariable\Model\ResourceModel\LoadLabelIdsWithQtyVariable;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Store\Model\StoreManagerInterface;

class GetLabelIdsWithQtyVariable
{
    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoadLabelIdsWithQtyVariable
     */
    private $loadLabelIdsWithQtyVariable;

    public function __construct(
        ModuleManager $moduleManager,
        StoreManagerInterface $storeManager,
        LoadLabelIdsWithQtyVariable $loadLabelIdsWithQtyVariable
    ) {

        $this->moduleManager = $moduleManager;
        $this->storeManager = $storeManager;
        $this->loadLabelIdsWithQtyVariable = $loadLabelIdsWithQtyVariable;
    }

    /**
     * @param int[] $productIds
     * @param int $storeId
     * @return int[]
     */
    public function execute(array $productIds, int $storeId): array
    {
        if ($this->moduleManager->isEnabled('Magento_Inventory')) {
            $storeIds = $this->storeManager->getStore($storeId)->getWebsite()->getStoreIds();
        } else {
            $storeIds = array_keys($this->storeManager->getStores());
        }

        return array_map(static function ($labelId) {
            return (int)$labelId;
        }, $this->loadLabelIdsWithQtyVariable->execute($productIds, $storeIds));
    }
}
