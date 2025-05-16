<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Improved Layered Navigation Base for Magento 2
 */

namespace Amasty\Shopby\Plugin\Ajax;

use Amasty\Shopby\Helper\Data;
use Amasty\Shopby\Helper\State;
use Amasty\Shopby\Model\Ajax\Counter\CounterDataProvider;
use Amasty\Shopby\Model\Layer\Cms\Manager;
use Magento\Catalog\Model\Category;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Url\Decoder;
use Magento\Framework\Url\Encoder;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Layout\Element;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Result\Page;

class Ajax
{
    public const OSN_CONFIG = 'amasty.xnotif.config';
    public const QUICKVIEW_CONFIG = 'amasty.quickview.config';
    public const SORTING_CONFIG = 'amasty.sorting.direction';
    public const ILN_FILTER_ANALYTICS = 'amasty.shopby.filter_analytics';
    public const CUSTOM_THEME_LAYOUT_MAPPING = [
        'fcnet/blank_julbo'=> [
            'image' => 'category.image',
            'description' => 'category_desc_main_column'
        ],
        'Smartwave/Porto' => [
            'image' => 'category.image',
            'description' => 'category_desc_main_column'
        ],
        'Amasty/JetTheme' => [
            'image' => 'category.image',
            'description' => 'category.description'
        ]
    ];

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var Encoder
     */
    protected $urlEncoder;

    /**
     * @var Decoder
     */
    protected $urlDecoder;

    /**
     * @var State
     */
    protected $stateHelper;

    /**
     * @var DesignInterface
     */
    protected $design;

    /**
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var Manager
     */
    private $cmsManager;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var Config
     */
    private $pageConfig;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var CounterDataProvider
     */
    protected $counterDataProvider;

    public function __construct(
        Data $helper,
        RawFactory $resultRawFactory,
        Encoder $urlEncoder,
        Decoder $urlDecoder,
        State $stateHelper,
        DesignInterface $design,
        ActionFlag $actionFlag,
        Manager $cmsManager,
        LayoutInterface $layout,
        Config $pageConfig,
        DataObjectFactory $dataObjectFactory,
        ManagerInterface $eventManager,
        CounterDataProvider $counterDataProvider
    ) {
        $this->helper = $helper;
        $this->resultRawFactory = $resultRawFactory;
        $this->urlEncoder = $urlEncoder;
        $this->urlDecoder = $urlDecoder;
        $this->stateHelper = $stateHelper;
        $this->design = $design;
        $this->actionFlag = $actionFlag;
        $this->cmsManager = $cmsManager;
        $this->layout = $layout;
        $this->pageConfig = $pageConfig;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->eventManager = $eventManager;
        $this->counterDataProvider = $counterDataProvider;
    }

    /**
     * @param RequestInterface $request
     * @return bool
     */
    protected function isAjax(RequestInterface $request)
    {
        if (!$request instanceof Http) {
            return false;
        }
        $isAjax = $request->isXmlHttpRequest() && $request->isAjax() && $request->getParam('shopbyAjax', false);
        $isScroll = $request->getParam('is_scroll');
        return $this->helper->isAjaxEnabled() && $isAjax && !$isScroll;
    }

    protected function isCounterRequest(RequestInterface $request): bool
    {
        if (!$request instanceof Http) {
            return false;
        }

        return (bool)$request->getParam('shopbyCounterAjax', false);
    }

    /**
     * @param Page|null $page
     *
     * @return array
     */
    protected function getAjaxResponseData($page = null)
    {
        $layout = $this->layout;
        $tags = [];

        $products = $layout->getBlock('category.products');
        if (!$products) {
            $products = $layout->getBlock('search.result');
        }

        $productList = null;

        $categoryProducts = $products ? $this->applyEventChanges($products->toHtml()) : '';

        $navigation = $layout->getBlock('catalog.leftnav') ?: $layout->getBlock('catalogsearch.leftnav');
        if ($navigation) {
            $navigation->toHtml();
            $tags = $this->addXTagCache($navigation, $tags);
        }

        $applyButton = $layout->getBlock('amasty.shopby.applybutton.sidebar');
        $tags = $this->addXTagCache($applyButton, $tags);

        $jsInit = $layout->getBlock('amasty.shopby.jsinit');
        $tags = $this->addXTagCache($jsInit, $tags);

        $navigationTop = null;
        if (strpos($categoryProducts, 'amasty-catalog-topnav') === false) {
            $navigationTop = $layout->getBlock('amshopby.catalog.topnav');
            $tags = $this->addXTagCache($navigationTop, $tags);
        }

        $applyButtonTop = $layout->getBlock('amasty.shopby.applybutton.topnav');
        $tags = $this->addXTagCache($applyButtonTop, $tags);

        $h1 = $layout->getBlock('page.main.title');
        $tags = $this->addXTagCache($h1, $tags);

        $title = $this->pageConfig->getTitle();
        $breadcrumbs = $layout->getBlock('breadcrumbs');
        $tags = $this->addXTagCache($breadcrumbs, $tags);

        $htmlCategoryData = '';
        $children = $layout->getChildNames('category.view.container');
        foreach ($children as $child) {
            $htmlCategoryData .= $layout->renderElement($child);
            $tags = $this->addXTagCache($child, $tags);
        }

        $shopbyCollapse = $layout->getBlock('catalog.navigation.collapsing');
        $shopbyCollapseHtml = '';
        if ($shopbyCollapse) {
            $shopbyCollapseHtml = $shopbyCollapse->toHtml();
            $tags = $this->addXTagCache($shopbyCollapse, $tags);
        }

        $swatchesChoose = $layout->getBlock('catalog.navigation.swatches.choose');
        $swatchesChooseHtml = '';
        if ($swatchesChoose) {
            $swatchesChooseHtml = $swatchesChoose->toHtml();
        }

        if ($products) {
            $tags = $this->addXTagCache($products, $tags);
            $productList = $products->getChildBlock('product_list') ?: $products->getChildBlock('search_result_list');
        }

        $currentCategory = $productList && $productList->getLayer()
            ? $productList->getLayer()->getCurrentCategory()
            : false;
        $isDisplayModePage = $currentCategory && $currentCategory->getDisplayMode() == Category::DM_PAGE;
        $responseData = [
            'categoryProducts'=> $categoryProducts . $swatchesChooseHtml . $this->getAdditionalConfigs($layout),
            'navigation' =>
                ($navigation ? $navigation->toHtml() : '')
                . $shopbyCollapseHtml
                . ($applyButton ? $applyButton->toHtml() : ''),
            'navigationTop' =>
                ($navigationTop ? $navigationTop->toHtml() : '')
                . ($applyButtonTop ? $applyButtonTop->toHtml() : ''),
            'breadcrumbs' => $breadcrumbs ? $breadcrumbs->toHtml() : '',
            'h1' => $h1 ? $h1->toHtml() : '',
            'title' => $title->get(),
            'bottomCmsBlock' => $this->getBlockHtml($layout, 'amshopby.bottom'),
            'url' => $this->stateHelper->getCurrentUrl(),
            'tags' => implode(',', array_unique($tags + [\Magento\PageCache\Model\Cache\Type::CACHE_TAG])),
            'js_init' => $jsInit ? $jsInit->toHtml() : '',
            'isDisplayModePage' => $isDisplayModePage,
            'currentCategoryId' => $currentCategory ? $currentCategory->getId() ?: 0 : 0,
            'currency' => $this->getBlockHtml($layout, 'currency'),
            'store' => $this->getBlockHtml($layout, 'store_language'),
            'store_switcher' => $this->getBlockHtml($layout, 'store_switcher'),
            'behaviour' => $this->getBlockHtml($layout, 'wishlist_behaviour')
        ];

        $productsCount = $productList
            ? $productList->getLoadedProductCollection()->getSize()
            : $products->getResultCount();

        $responseData['productsCount'] = $productsCount;

        if ($layout->getBlock('category.amshopby.ajax')) {
            $responseData['newClearUrl'] = $layout->getBlock('category.amshopby.ajax')->getClearUrl();
        }

        $this->addCategoryData($htmlCategoryData, $layout, $responseData);

        try {
            $sidebarTag = $layout->getElementProperty('div.sidebar.additional', Element::CONTAINER_OPT_HTML_TAG);
            $sidebarClass = $layout->getElementProperty('div.sidebar.additional', Element::CONTAINER_OPT_HTML_CLASS);
            $sidebarAdditional = $layout->renderNonCachedElement('div.sidebar.additional');
            $responseData['sidebar_additional'] = $sidebarAdditional;
            $responseData['sidebar_additional_alias'] = $sidebarTag . '.' . str_replace(' ', '.', $sidebarClass);
        } catch (\Exception $e) {
            unset($responseData['sidebar_additional']);
        }

        $responseData = $this->removeAjaxParam($responseData);
        $responseData = $this->removeEncodedAjaxParams($responseData);

        return $responseData;
    }

    /**
     * @param $responseData
     * @param $htmlCategoryData
     * @param $layout
     */
    private function addCategoryData($htmlCategoryData, $layout, &$responseData)
    {
        $themeCode = $this->design->getDesignTheme()->getCode();
        if (array_key_exists($themeCode, self::CUSTOM_THEME_LAYOUT_MAPPING)) {
            $responseData['image'] = $this->getBlockHtml(
                $layout,
                self::CUSTOM_THEME_LAYOUT_MAPPING[$themeCode]['image']
            );
            $responseData['description'] = $this->getBlockHtml(
                $layout,
                self::CUSTOM_THEME_LAYOUT_MAPPING[$themeCode]['description']
            );
        } else {
            // @codingStandardsIgnoreStart
            $htmlCategoryData = '<div class="category-view">' . $htmlCategoryData . '</div>';
            // @codingStandardsIgnoreEnd
            $responseData['categoryData'] = $htmlCategoryData;
            $responseData['description'] = $layout->getBlock('category.description')
                ? $layout->renderElement('category.description')
                : '';
        }
    }

    /**
     * @param $layout
     * @param $blockName
     * @return string
     */
    private function getBlockHtml($layout, $blockName)
    {
        return $layout->getBlock($blockName) ? $layout->getBlock($blockName)->toHtml() : '';
    }

    /**
     * @param mixed $element
     * @param array $tags
     * @return array
     */
    private function addXTagCache($element, array $tags)
    {
        if ($element instanceof IdentityInterface) {
            $tags = array_merge($tags, $element->getIdentities());
        }

        return $tags;
    }

    /**
     * @param array $responseData
     * @return array
     */
    private function removeEncodedAjaxParams(array $responseData)
    {
        $pattern = '@aHR0c(Dov|HM6)[A-Za-z0-9_-]+@u';
        array_walk($responseData, function (&$html) use ($pattern) {
            // 'aHR0cDov' and 'aHR0cHM6' are the beginning of the Base64 code for 'http:/' and 'https:'
            $res = preg_replace_callback($pattern, [$this, 'removeAjaxParamFromEncodedMatch'], $html);
            if ($res !== null) {
                $html = $res;
            }
        });

        return $responseData;
    }

    /**
     * @param array $match
     * @return string
     */
    protected function removeAjaxParamFromEncodedMatch($match)
    {
        $originalUrl = $this->urlDecoder->decode($match[0]);
        if ($originalUrl === false) {
            return $match[0];
        }
        $url = $this->removeAjaxParam($originalUrl);
        return ($originalUrl == $url) ? $match[0] : rtrim($this->urlEncoder->encode($url), ',');
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function removeAjaxParam($data)
    {
        $data = str_replace([
            '?shopbyAjax=1&amp;',
            '?shopbyAjax=1&',
        ], '?', $data);
        $data = str_replace([
            '?shopbyAjax=1',
            '&amp;shopbyAjax=1',
            '&shopbyAjax=1',
        ], '', $data);

        return $data;
    }

    /**
     * @param array $data
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    protected function prepareResponse(array $data)
    {
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        if (isset($data['tags'])) {
            $response->setHeader('X-Magento-Tags', $data['tags']);
            unset($data['tags']);
        }

        $response->setContents(json_encode($data));
        return $response;
    }

    /**
     * @param $layout
     * @return string
     */
    private function getAdditionalConfigs($layout)
    {
        $html = '';
        $html .= $this->getBlockHtml($layout, self::OSN_CONFIG);
        $html .= $this->getBlockHtml($layout, self::QUICKVIEW_CONFIG);
        $html .= $this->getBlockHtml($layout, self::SORTING_CONFIG);
        $html .= $this->getBlockHtml($layout, self::ILN_FILTER_ANALYTICS);

        return $html;
    }

    /**
     * @return Manager
     */
    public function getCmsManager()
    {
        return $this->cmsManager;
    }

    /**
     * @return ActionFlag
     */
    public function getActionFlag()
    {
        return $this->actionFlag;
    }

    /**
     * Compatibility with Google Page SpeedOptimizer
     * @param string $html
     *
     * @return string|mixed
     */
    protected function applyEventChanges(string $html)
    {
        $dataObject = $this->dataObjectFactory->create(
            [
                'data' => [
                    'page' => $html,
                    'pageType' => 'catalog_category_view'
                ]
            ]
        );
        $this->eventManager->dispatch('amoptimizer_process_ajax_page', ['data' => $dataObject]);
        $html = $dataObject->getData('page');

        return $html;
    }
}
