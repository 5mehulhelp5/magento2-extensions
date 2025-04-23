<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Api\Data;

/**
 * @api
 */
interface RenderSettingsInterface
{
    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     */
    public function getProduct(): ?\Magento\Catalog\Api\Data\ProductInterface;

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return void
     */
    public function setProduct(\Magento\Catalog\Api\Data\ProductInterface $product): void;

    /**
     * @return bool
     */
    public function isLabelVisible(): bool;

    /**
     * @param bool $isVisible
     * @return void
     */
    public function setIsLabelVisible(bool $isVisible): void;
}
