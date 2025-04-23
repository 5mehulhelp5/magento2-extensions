<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Rich Snippets for Magento 2
 */

namespace Amasty\SeoRichData\Model\JsonLd\Processor;

use Amasty\SeoRichData\Helper\Config as ConfigHelper;
use Amasty\SeoRichData\Model\JsonLd\ProductInfo;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;

class Category implements ProcessorInterface
{
    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var LayerResolver
     */
    private $layerResolver;

    /**
     * @var Registry
     */
    private $coreRegistry = null;

    /**
     * @var ProductInfo
     */
    private $productInfo;

    public function __construct(
        ConfigHelper $configHelper,
        RequestInterface $request,
        LayerResolver $layerResolver,
        Registry $coreRegistry,
        ProductInfo $productInfo
    ) {
        $this->configHelper = $configHelper;
        $this->request = $request;
        $this->layerResolver = $layerResolver;
        $this->coreRegistry = $coreRegistry;
        $this->productInfo = $productInfo;
    }

    public function process(array $data): array
    {
        if (!$this->configHelper->forCategoryEnabled()) {
            return $data;
        }

        $category = $this->getCurrentCategory();
        if (!$category) {
            return $data;
        }

        if ('category' != $this->request->getControllerName()) {
            return $data;
        }

        $data['category'] = $this->generateProductsInfo();

        return $data;
    }

    private function generateProductsInfo(): array
    {
        $productCollection = $this->layerResolver->get()->getProductCollection();
        $productsInfo = [];
        foreach ($productCollection as $product) {
            $productsInfo[] = $this->productInfo->extract($product);
        }

        return $productsInfo;
    }

    private function getCurrentCategory(): ?CategoryInterface
    {
        return $this->coreRegistry->registry('current_category');
    }
}
