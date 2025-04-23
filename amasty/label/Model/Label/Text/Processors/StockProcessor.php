<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\Text\Processors;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Exceptions\TextRenderException;
use Amasty\Label\Model\Label\Text\ProcessorInterface;
use Amasty\Label\Model\Product\GetQty;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;

class StockProcessor implements ProcessorInterface
{
    public const STOCK = 'STOCK';

    /**
     * @var GetQty
     */
    private $getQty;

    public function __construct(
        GetQty $getQty
    ) {
        $this->getQty = $getQty;
    }

    public function getAcceptableVariables(): array
    {
        return [
            self::STOCK
        ];
    }

    public function getVariableValue(string $variable, LabelInterface $label, ProductInterface $product): string
    {
        switch ($variable) {
            case self::STOCK:
                return (string) $this->getProductQty($product);
            default:
                throw new TextRenderException(__('The passed variable could not be processed'));
        }
    }

    private function getProductQty(Product $product): ?float
    {
        return $this->getQty->execute($product);
    }
}
