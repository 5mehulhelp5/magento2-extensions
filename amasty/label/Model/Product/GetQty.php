<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Product;

use Amasty\Label\Model\ResourceModel\Inventory;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\GroupedProduct\Model\Product\Type\Grouped;

class GetQty
{
    /**
     * @var Inventory
     */
    private $inventory;

    /**
     * @var array
     */
    private $qty;

    public function __construct(
        Inventory $inventory
    ) {
        $this->inventory = $inventory;
    }

    /**
     * Retrieve qty for product.
     * For composite(configurable, grouped) retrieved sum of child qty.
     *
     * @param Product $product
     * @param string|null $sourceCode If need qty for specific inventory source.
     * @return float
     */
    public function execute(Product $product, ?string $sourceCode = null): float
    {
        if (!empty($this->qty[$product->getId()])) {
            return $this->qty[$product->getId()];
        }

        switch ($product->getTypeId()) {
            case Configurable::TYPE_CODE:
                $products = $product->getTypeInstance()->getUsedProducts($product);
                $this->qty[$product->getId()] = $this->getQtySum($products, $sourceCode);
                break;
            case Grouped::TYPE_CODE:
                $products = $product->getTypeInstance()->getAssociatedProducts($product);
                $this->qty[$product->getId()] = $this->getQtySum($products, $sourceCode);
                break;
            default:
                $this->qty[$product->getId()] = $this->getQty($product, $sourceCode);
                break;
        }

        return $this->qty[$product->getId()];
    }

    private function getQty(Product $product, ?string $sourceCode): float
    {
        if ($sourceCode === null) {
            $qty = $this->inventory->getQty(
                $product->getData('sku'),
                $product->getStore()->getWebsite()->getCode()
            );
        } else {
            $qty = $this->inventory->getItemQtyBySource(
                $product->getData('sku'),
                $sourceCode
            );
        }

        return $qty;
    }

    /**
     * @param Product[] $products
     * @param string|null $sourceCode
     * @return float
     */
    private function getQtySum(array $products, ?string $sourceCode): float
    {
        $quantity = 0.0;

        foreach ($products as $simple) {
            $simpleQty = $this->getQty($simple, $sourceCode);

            if ($simpleQty > 0) {
                $quantity += $simpleQty;
            }
        }

        return $quantity;
    }
}
