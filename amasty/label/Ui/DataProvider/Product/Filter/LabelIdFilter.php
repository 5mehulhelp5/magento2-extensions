<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Ui\DataProvider\Product\Filter;

use Amasty\Label\Model\ResourceModel\Indexer\IndexedProductDataProvider;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Data\Collection;
use Magento\Ui\DataProvider\AddFilterToCollectionInterface;

class LabelIdFilter implements AddFilterToCollectionInterface
{
    public const MATCHED_FLAG = 'matched_products';

    /**
     * @var IndexedProductDataProvider
     */
    private $indexedProductDataProvider;

    public function __construct(
        IndexedProductDataProvider $indexedProductDataProvider
    ) {
        $this->indexedProductDataProvider = $indexedProductDataProvider;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param ProductCollection|Collection $collection
     * @param string $field
     * @param null $condition
     */
    public function addFilter(Collection $collection, $field, $condition = null)
    {
        if ($collection->getFlag(self::MATCHED_FLAG) || empty($condition['eq'])) {
            return;
        }
        $indexedProductIds = $this->indexedProductDataProvider->getIndexedProductIds((int)$condition['eq']);

        if ($indexedProductIds) {
            $collection->addIdFilter($indexedProductIds);
        } else {
            $collection->getSelect()->where('null');
        }

        $collection->setFlag(self::MATCHED_FLAG, $indexedProductIds);
    }
}
