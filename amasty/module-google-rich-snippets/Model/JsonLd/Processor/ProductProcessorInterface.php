<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Rich Snippets for Magento 2
 */

namespace Amasty\SeoRichData\Model\JsonLd\Processor;

use Magento\Catalog\Model\Product as ProductModel;

interface ProductProcessorInterface
{
    public function process(array $data, ProductModel $product): array;
}
