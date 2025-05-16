<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Plugins\ConfigurableProduct\Model\Product\Type;

use Magento\CatalogInventory\Model\ResourceModel\Stock\Status as StockStatusResource;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as NativeConfigurable;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\Collection;

class Configurable
{
    public const STOCK_FLAG = 'has_stock_status_filter';

    /**
     * @var StockStatusResource
     */
    private $stockStatusResource;

    public function __construct(StockStatusResource $stockStatusResource)
    {
        $this->stockStatusResource = $stockStatusResource;
    }

    /**
     * @param NativeConfigurable $subject
     * @param Collection $collection
     *
     * @return Collection
     */
    public function afterGetUsedProductCollection(NativeConfigurable $subject, $collection)
    {
        if (!$collection->hasFlag(self::STOCK_FLAG)) {
            $collection->setFlag(self::STOCK_FLAG, true);
            $this->stockStatusResource->addStockDataToCollection($collection, false);
        }

        return $collection;
    }
}
