<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 * @noinspection PhpDeprecationInspection
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Block;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product as ModelProduct;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Url\EncoderInterface;
use Magento\Widget\Block\BlockInterface;

class Product extends AbstractProduct implements BlockInterface
{

    /**
     * @var EncoderInterface|mixed|null
     */
    protected $urlEncoder;
    /**
     * @var RedirectInterface
     */
    protected $redirect;

    /**
     * Product constructor.
     * @param Context $context
     * @param RedirectInterface $redirect
     * @param array $data
     * @param EncoderInterface|null $urlEncoder
     */
    public function __construct(
        Context $context,
        RedirectInterface $redirect,
        array $data = [],
        EncoderInterface $urlEncoder = null
    ) {
        $this->redirect = $redirect;
        $this->urlEncoder = $urlEncoder ?: ObjectManager::getInstance()->get(EncoderInterface::class);
        parent::__construct($context, $data);
    }

    /**
     * Get post parameters.
     *
     * @param ModelProduct $product
     * @return array
     */
    public function getAddToCartPostParams(ModelProduct $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlEncoder->encode($url),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAddToCartUrl($product, $additional = [])
    {
        $requestingPageUrl = $this->redirect->getRefererUrl();

        if (!empty($requestingPageUrl)) {
            $additional['useUencPlaceholder'] = true;
            $url = parent::getAddToCartUrl($product, $additional);
            return str_replace('%25uenc%25', (string)$this->urlEncoder->encode($requestingPageUrl), $url);
        }

        return parent::getAddToCartUrl($product, $additional);
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @throws LocalizedException
     */
    public function getProductPriceHtml(
        ModelProduct $product,
        $priceType = null,
        $renderZone = Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

        /** @var Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        if (!$priceRender) {
            $priceRender = $this->getLayout()->createBlock(
                Render::class,
                'product.price.render.default',
                ['data' => ['price_render_handle' => 'catalog_product_prices']]
            );
        }

        $price = $priceRender->render(
            FinalPrice::PRICE_CODE,
            $product,
            $arguments
        );

        return $price;
    }
}
