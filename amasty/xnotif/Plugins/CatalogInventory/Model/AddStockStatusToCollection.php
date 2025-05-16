<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Plugins\CatalogInventory\Model;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogInventory\Helper\Stock;

/**
 * Catalog inventory module plugin
 */
class AddStockStatusToCollection
{
    /**
     * @var Stock
     */
    protected $stockHelper;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    public function __construct(
        Stock $stockHelper,
        UserContextInterface $userContext
    ) {
        $this->stockHelper = $stockHelper;
        $this->userContext = $userContext;
    }

    /**
     * Add stock filter to collection
     */
    public function beforeLoad(
        Collection $productCollection,
        bool $printQuery = false,
        bool $logQuery = false
    ): array {
        if (!$this->getUserType() == UserContextInterface::USER_TYPE_ADMIN) {
            $this->stockHelper->addIsInStockFilterToCollection($productCollection);
        }

        return [$printQuery, $logQuery];
    }

    /**
     * Return user type
     */
    private function getUserType(): ?int
    {
        return $this->userContext->getUserType();
    }
}
