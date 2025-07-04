<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\Parts;

use Amasty\Label\Api\Data\RenderSettingsInterface;
use Magento\Catalog\Api\Data\ProductInterface;

class RenderSettings implements RenderSettingsInterface
{
    /**
     * @var ?ProductInterface
     */
    private $product;

    /**
     * @var ?ProductInterface
     */
    private $parentProduct;

    /**
     * @var bool
     */
    private $isLabelVisible = true;

    public function getProduct(): ?ProductInterface
    {
        return $this->product;
    }

    public function setProduct(ProductInterface $product): void
    {
        $this->product = $product;
    }

    public function getParentProduct(): ?ProductInterface
    {
        return $this->parentProduct;
    }

    public function setParentProduct(ProductInterface $product): void
    {
        $this->parentProduct = $product;
    }

    public function isLabelVisible(): bool
    {
        return $this->isLabelVisible;
    }

    public function setIsLabelVisible(bool $isVisible): void
    {
        $this->isLabelVisible = $isVisible;
    }
}
