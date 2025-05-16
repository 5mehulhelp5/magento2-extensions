<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Frontend\ProductList;

use Amasty\Xnotif\Block\Catalog\Category\StockSubscribe;
use Amasty\Xnotif\Helper\Data as XnotifHelper;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableModel;
use Magento\Customer\Model\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\LayoutInterface;

class GenerateSubscribeHtml
{
    public const PRODUCT_LIST_BLOCK_NAME = 'category.subscribe.block';

    /**
     * @var LayoutInterface
     */
    private $layout;
    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var XnotifHelper
     */
    private $xnotifHelper;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var string[]
     */
    private $childBlocks;

    public function __construct(
        LayoutInterface $layout,
        HttpContext $httpContext,
        XnotifHelper $xnotifHelper,
        Registry $registry,
        array $childBlocks = []
    ) {
        $this->layout = $layout;
        $this->httpContext = $httpContext;
        $this->xnotifHelper = $xnotifHelper;
        $this->registry = $registry;
        $this->childBlocks = $childBlocks;
    }

    public function execute(ProductInterface $product): string
    {
        $html = '';

        if (!$product->isSaleable() && !$product->getData('amxnotif_hide_alert')) {
            $html = $this->generateAlertHtml($product);
        }

        if ($product->getTypeId() === ConfigurableModel::TYPE_CODE) {
            $html .= $this->generateConfigurableHtml($product);
        }

        return $html;
    }

    private function generateAlertHtml(ProductInterface $product): string
    {
        $subscribeBlock = $this->getSubscribeBlock();

        $currentProduct = $this->registry->registry('current_product');

        /*check if it is child product for replace product registered to child product.*/
        $isChildProduct = $currentProduct && ($currentProduct->getId() != $product->getId());
        if ($isChildProduct) {
            $subscribeBlock->setData('parent_product_id', $currentProduct->getId());
            $subscribeBlock->setOriginalProduct($product);
        }

        $this->prepareSubscribeBlock($subscribeBlock, $product);
        foreach ($this->childBlocks as $name => $block) {
            $subscribeBlock->setChild(
                $name,
                $this->layout->createBlock($block)
            );
        }

        return $subscribeBlock->toHtml();
    }

    private function getSubscribeBlock(): BlockInterface
    {
        $subscribeBlock = $this->layout->getBlock(self::PRODUCT_LIST_BLOCK_NAME);
        if (!$subscribeBlock) {
            $subscribeBlock = $this->layout->createBlock(StockSubscribe::class, self::PRODUCT_LIST_BLOCK_NAME);
        }

        return $subscribeBlock;
    }

    private function generateConfigurableHtml(ProductInterface $product): string
    {
        return sprintf('<div class="amxnotif-category-container" data-amsubscribe="%d"></div>', $product->getId());
    }

    private function prepareSubscribeBlock(BlockInterface $subscribeBlock, ProductInterface $product): void
    {
        if ($this->httpContext->getValue(Context::CONTEXT_AUTH)) {
            $subscribeBlock->setTemplate('Amasty_Xnotif::product/view.phtml');
            $subscribeBlock->setHtmlClass('alert stock link-stock-alert');
            $subscribeBlock->setSignupLabel(
                __('Notify me when this product is in stock')
            );
            $subscribeBlock->setSignupUrl(
                $this->xnotifHelper->getSignupUrl(
                    'stock',
                    $product->getId(),
                    $subscribeBlock->getData('parent_product_id')
                )
            );
        } else {
            $subscribeBlock->setTemplate('Amasty_Xnotif::category/subscribe.phtml');
            $subscribeBlock->setOriginalProduct($product);
        }
    }
}
