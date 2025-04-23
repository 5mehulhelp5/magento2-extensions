<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 * @noinspection PhpDeprecationInspection
 */

namespace Olegnax\InstagramFeedPro\Controller\Api;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\FormKey;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\StoreManagerInterface;
use Olegnax\InstagramFeedPro\Helper\Helper;
use Olegnax\InstagramFeedPro\Model\HotSpot as ModelHotSpot;
use Olegnax\InstagramFeedPro\Model\IntsPost;
use Olegnax\InstagramFeedPro\Model\Config\Source\RelatedLayout;

class Related extends Action
{
    const AJAX_ATTR = 'id';
    const TEMPLATE_PRODUCT = 'Olegnax_InstagramFeedPro::related/products%s.phtml';
    const TEMPLATE_HOTSPOT = 'Olegnax_InstagramFeedPro::related/hotspot.phtml';
    /**
     * @var Helper
     */
    protected $helper;
    /**
     * @var LayoutInterface
     */
    protected $layout;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var Json|mixed|null
     */
    protected $json;
    /**
     * @var IntsPost
     */
    protected $intsPost;
    /**
     * @var int
     */
    protected $_storeId;
    /**
     * @var string
     */
    private $relatedLayout;

    /**
     * Product constructor.
     * @param Context $context
     * @param Helper $helper
     * @param LayoutInterface $layout
     * @param StoreManagerInterface $storeManager
     * @param IntsPost $intsPost
     * @param Json|null $json
     */
    public function __construct(
        Context $context,
        Helper $helper,
        LayoutInterface $layout,
        StoreManagerInterface $storeManager,
        IntsPost $intsPost,
        Json $json = null
    ) {
        $this->helper = $helper;
        $this->layout = $layout;
        $this->storeManager = $storeManager;
        $this->intsPost = $intsPost;
        $this->json = $json ?: ObjectManager::getInstance()->get(Json::class);
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $data = [
            'hotSpots' => [],
            'products' => [],
        ];
        if ($this->isAjax()) {
            $id = $this->getRequest()->getParam(static::AJAX_ATTR, '');
            $this->intsPost->load($id);
            if ($id == $this->intsPost->getIntspostId()) {
                $productSku = null;
                /** @var ModelHotSpot $related */
                foreach ($this->intsPost->getRelated() as $related) {
                    /** @var Product $product */
                    $product = $related->getProduct();
                    if ($product) {
                        $productSku = $product->getSku();
                        $data['products'][$product->getId()] = $this->createProductBlock($product);
                    }
                    if ($related->getMarkerStyle()) {
                        $data['hotSpots'][] = $this->createHotSpotBlock($related);
                    }
                }

                $showCart = true; // @todo set variabel
                if ($productSku && $showCart) {
                    $data['products'][] = '<script type="text/x-magento-init">
{"[data-role=tocart-form], .form.map.checkout":{"catalogAddToCart":{"product_sku":"' . $productSku . '"}}}
</script>';
                }
            }
        }
        $data['products'] = implode('', $data['products']);
        $data['hotSpots'] = implode('', $data['hotSpots']);

        return $this->getResponse()->representJson(
            $this->json->serialize($data)
        );
    }

    /**
     * @return bool
     */
    protected function isAjax()
    {
        return $this->helper->isEnabled() &&
            $this->getRequest()->isXmlHttpRequest() &&
            $this->getRequest()->isAjax() &&
            $this->getRequest()->getParam(static::AJAX_ATTR, '');
    }

    /**
     * @param Product $product
     * @param array $data
     * @return string
     */
    protected function createProductBlock(Product $product, $data = [])
    {
        $formkey = $this->layout->createBlock(FormKey::class)->toHtml();

        return $this->layout->createBlock(
            \Olegnax\InstagramFeedPro\Block\Product::class,
            '',
            [
                'data' => array_replace([
                    'product' => $product,
                    'formkey' => $formkey,
                ], $data),
            ]
        )->setTemplate(sprintf(static::TEMPLATE_PRODUCT, $this->getRelatedLayout()))->toHtml();
    }

    /**
     * @return string
     */
    private function getRelatedLayout()
    {
        if (null === $this->relatedLayout) {
            /** @var string $data */
            $data = $this->helper->getModuleConfig('modal/related_layout');
            if ($data == RelatedLayout::V3) {
                $this->relatedLayout = '-gridv2';
            } else {
                $this->relatedLayout = '';
            }

        }

        return $this->relatedLayout;
    }

    /**
     * @param ModelHotSpot $hotspot
     * @param array $data
     * @return string
     */
    protected function createHotSpotBlock(ModelHotSpot $hotspot, $data = [])
    {
        return $this->layout->createBlock(
            Template::class,
            '',
            [
                'data' => array_replace([
                    'hot_spot' => $hotspot,
                ], $data),
            ]
        )->setTemplate(static::TEMPLATE_HOTSPOT)->toHtml();
    }

    /**
     * Return current store id
     *
     * @return int
     * @throws NoSuchEntityException
     */
    public function getStoreId()
    {
        if ($this->_storeId === null) {
            $this->_storeId = $this->storeManager->getStore()->getId();
        }
        return $this->_storeId;
    }
}
