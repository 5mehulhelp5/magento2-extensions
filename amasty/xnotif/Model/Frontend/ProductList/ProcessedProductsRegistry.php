<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Frontend\ProductList;

class ProcessedProductsRegistry
{
    /**
     * @var array ['block_name' => [productId, ...], ...]
     */
    private $processedProducts = [];

    public function markProcessedProduct(string $blockName, int $productId): void
    {
        $this->processedProducts[$blockName][$productId] = $productId;
    }

    public function checkIfProductProcessed(string $blockName, int $productId): bool
    {
        return isset($this->processedProducts[$blockName][$productId]);
    }

    public function isProcessedProductsExists(): bool
    {
        return count($this->processedProducts) > 0;
    }
}
