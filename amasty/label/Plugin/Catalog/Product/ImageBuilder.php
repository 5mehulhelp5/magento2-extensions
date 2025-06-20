<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Plugin\Catalog\Product;

class ImageBuilder
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * ImageBuilder constructor.
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param \Magento\Catalog\Block\Product\ImageBuilder $subject
     * @param $result
     * @param bool $product|\Magento\Catalog\Model\Product
     * @return mixed
     */
    public function afterCreate(
        \Magento\Catalog\Block\Product\ImageBuilder $subject,
        $result,
        $product = false
    ) {
        if (is_object($result)) {
            $product = $product ?: $this->registry->registry('amlabel_current_product');
            $result->setProduct($product);
        }

        return $result;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param \Magento\Catalog\Block\Product\ImageBuilder $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     */
    public function aroundSetProduct(
        \Magento\Catalog\Block\Product\ImageBuilder $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
        $result = $proceed($product);
        $this->registry->unregister('amlabel_current_product');
        $this->registry->register('amlabel_current_product', $product);

        return $result;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param \Magento\Catalog\Block\Product\ImageBuilder $subject
     * @param string $imageId
     * @return array
     */
    public function beforeSetImageId(
        \Magento\Catalog\Block\Product\ImageBuilder $subject,
        $imageId
    ) {
        if ($imageId == 'cart_page_product_thumbnail') {
            $this->registry->unregister('amlabel_current_product');
        }

        return [$imageId];
    }
}
