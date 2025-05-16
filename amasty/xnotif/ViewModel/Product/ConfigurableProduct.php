<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\ViewModel\Product;

use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Swatches\Model\SwatchAttributesProvider;

class ConfigurableProduct implements ArgumentInterface
{
    /**
     * @var SwatchAttributesProvider
     */
    private $swatchAttributesProvider;

    public function __construct(SwatchAttributesProvider $swatchAttributesProvider)
    {
        $this->swatchAttributesProvider = $swatchAttributesProvider;
    }

    public function isProductWithoutSwatchAttribute(Product $product): bool
    {
        return $product->getTypeId() === Configurable::TYPE_CODE
            && empty($this->swatchAttributesProvider->provide($product));
    }
}
