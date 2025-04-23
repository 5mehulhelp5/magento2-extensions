<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Block\Widget;

use IntlDateFormatter;
use Magento\Catalog\Block\Product\Widget\Html\Pager;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;
use Olegnax\InstagramFeedPro\Block\Item\MediaType;
use Olegnax\InstagramFeedPro\Block\StyleBlock;
use Olegnax\InstagramFeedPro\Helper\Helper;
use Olegnax\InstagramFeedPro\Model\Config\Source\Users;
use Olegnax\InstagramFeedPro\Model\Data\IntsPost as IntsPostData;
use Olegnax\InstagramFeedPro\Model\IntsPost;
use Olegnax\InstagramFeedPro\Model\IntsUser;
use Olegnax\InstagramFeedPro\Model\ResourceModel\IntsPost\CollectionFactory as IntsPostCollectionFactory;
use Olegnax\Core\Model\DynamicStyle\EscapeCss;

/**
 * @method bool getAutoplay()
 * @method bool getDots()
 * @method bool getLoop()
 * @method bool getNav()
 * @method bool getPauseOnHover()
 * @method bool getRewind()
 * @method bool getVideoBehaviour()
 * @method int getAutoplayTime()
 * @method int getColumnsDesktop()
 * @method int getColumnsMobile()
 * @method int getColumnsTablet()
 * @method string getCacheLifetime()
 * @method string getCheckRelated()
 * @method string getDateFormat()
 * @method string getImageResize()
 * @method string getLazyLoad()
 * @method string getMediaType()
 * @method string getOwner()
 * @method string getShowPager()
 * @method string getTemplate()
 * @method void setMediaType($mediaType)
 * @method void setOwner($owner)
 */
class Instagram extends Template implements BlockInterface
{
    const TEMPLATE_HYVA_WISHLIST_JS = 'Magento_Catalog::product/list/js/wishlist.phtml';
    const TEMPLATE_CAROUSEL_JS = 'Olegnax_InstagramFeedPro::carousel-js.phtml';
    const TEMPLATE_MODAL = 'Olegnax_InstagramFeedPro::modal.phtml';
    const TEMPLATE_JS = 'Olegnax_InstagramFeedPro::video-js.phtml';
	const TEMPLATE_STYLES = 'Olegnax_InstagramFeedPro::style.phtml';
	const TEMPLATE_STYLES_GLOBAL = 'Olegnax_InstagramFeedPro::styles_global.phtml';
    /**
     * Default value for products count that will be shown
     */
    const DEFAULT_POSTS_COUNT = 10;

    /**
     * Name of request parameter for page number value
     *
     * @deprecated @see $this->getData('page_var_name')
     */
    const PAGE_VAR_NAME = 'np';

    /**
     * Default value for products per page
     */
    const DEFAULT_POSTS_PER_PAGE = 5;

    /**
     * Default value whether show pager or not
     */
    const DEFAULT_SHOW_PAGER = false;

    /**
     * @var string
     */
    protected $_template = "widget/instagram.phtml";
    /**
     * @var IntsPostCollectionFactory
     */
    protected $intsPostCollectionFactory;
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;
    /**
     * @var Helper
     */
    protected $helper;
    /**
     * @var Json
     */
    protected $json;
    /**
     * Instance of pager block
     *
     * @var Pager
     */
    protected $pager;
    /**
     * @var array
     */
    protected $users;

    /**
     * @var EscapeCss
     */
    protected $escapeCss;

    /**
     * Instagram constructor.
     *
     * @param Context $context
     * @param IntsPostCollectionFactory $intsPostCollectionFactory
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param Helper $helper
     * @param Users $users
     * @param array $data
     * @param Json|null $json
     */
    public function __construct(
        Context $context,
        IntsPostCollectionFactory $intsPostCollectionFactory,
        \Magento\Framework\App\Http\Context $httpContext,
        Helper $helper,
        Users $users,
        EscapeCss $escapeCss,
        array $data = [],
        Json $json = null
    ) {
        $this->intsPostCollectionFactory = $intsPostCollectionFactory;
        $this->httpContext = $httpContext;
        $this->helper = $helper;
        $this->users = $users->toArray();
        $this->escapeCss = $escapeCss;

        $this->json = $json ?: ObjectManager::getInstance()->get(Json::class);
        parent::__construct($context, $data);
    }

    /**
     * @param array $newVal
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getCacheKeyInfo($newVal = [])
    {
        return array_merge([
            'OLEGNAX_INSTAGRAMFEEDPRO_POSTS_LIST_WIDGET',
            $this->getStoreId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
            (int)$this->getRequest()->getParam($this->getData('page_var_name'), 1),
            $this->json->serialize($this->getRequest()->getParams()),
            $this->json->serialize($this->getData()),
        ], $newVal);
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @return string|string[]|null
     */
    public function getUnderlineNameInLayout()
    {
        $name = $this->getNameInLayout();
        $name = preg_replace('/[^a-zA-Z0-9_]/i', '_', $name);
        $name .= substr(md5(microtime()), -5);

        return $name;
    }

    /**
     * Render pagination HTML
     *
     * @return string
     * @throws LocalizedException
     */
    public function getPagerHtml()
    {
        if ($this->showPager() && $this->getImagesCollection()->getSize() > $this->getImagesPerPage()) {
            if (!$this->pager) {
                $this->pager = $this->getLayout()->createBlock(
                    Pager::class,
                    $this->getWidgetPagerBlockName()
                );

                /** @noinspection PhpUndefinedMethodInspection */
                $this->pager->setUseContainer(true)
                    ->setShowAmounts(true)
                    ->setShowPerPage(false)
                    ->setPageVarName($this->getData('page_var_name'))
                    ->setLimit($this->getImagesPerPage())
                    ->setTotalLimit($this->getImagesCount())
                    ->setCollection($this->getImagesCollection());
            }
            if ($this->pager instanceof AbstractBlock) {
                return $this->pager->toHtml();
            }
        }

        return '';
    }

    /**
     * Return flag whether pager need to be shown or not
     *
     * @return bool
     */
    public function showPager()
    {
        if (!$this->hasData('show_pager')) {
            $this->setData('show_pager', self::DEFAULT_SHOW_PAGER);
        }

        return (bool)$this->getData('show_pager');
    }

    /**
     * @return \Olegnax\InstagramFeedPro\Model\ResourceModel\IntsPost\Collection
     * @throws NoSuchEntityException
     */
    protected function initImagesCollection() {
        /** @var \Olegnax\InstagramFeedPro\Model\ResourceModel\IntsPost\Collection $collection */
        $collection = $this->intsPostCollectionFactory->create();
        if (!$this->helper->isEnabled()) {
            return $collection->addFieldToFilter('intspost_id', 0);
        }
        $collection->addFieldToSelect('*')
            ->addStoreFilter()
            ->addFieldToFilter(IntsPostData::IS_ACTIVE, 1)
            ->setOrder(
                IntsPostData::TIMESTAMP,
                Collection::SORT_ORDER_DESC
            );
        if ($this->getOwner()) {
            $owner = $this->getOwner();
            if (!is_array($owner)) {
                $owner = array_map('trim', explode(',', $owner));
                $this->setOwner($owner);
            }
            $collection->addFieldToFilter(IntsPostData::OWNER, ['in' => $owner]);
        }
        if ($this->getMediaType()) {
            $mediaType = $this->getMediaType();
            if (!is_array($mediaType)) {
                $mediaType = array_map('trim', explode(',', $mediaType));
                $this->setMediaType($mediaType);
            }
            $collection->addFieldToFilter(IntsPostData::MEDIA_TYPE, ['in' => $mediaType]);
        }

        return $collection;
    }

    /**
     * @return \Olegnax\InstagramFeedPro\Model\ResourceModel\IntsPost\Collection
     * @throws NoSuchEntityException
     */
    public function getImagesCollection()
    {
        $collection = $this->initImagesCollection();
        if (0 != $this->getCheckRelated()) {
            $collection->addRelatedProductFilter();
            if (-1 == $this->getCheckRelated() && !$collection->count()) {
                $collection = $this->initImagesCollection();
            }
        }

        return $collection->setPageSize($this->getPageSize())
            ->setCurPage($this->getRequest()->getParam($this->getData('page_var_name'), 1));
    }

    /**
     * Retrieve how many products should be displayed on page
     *
     * @return int
     */
    protected function getPageSize()
    {
        return $this->showPager() ? $this->getImagesPerPage() : $this->getImagesCount();
    }

    /**
     * Retrieve how many products should be displayed
     *
     * @return int
     */
    public function getImagesPerPage()
    {
        if (!$this->hasData('images_per_page')) {
            $this->setData('images_per_page', self::DEFAULT_POSTS_PER_PAGE);
        }

        return $this->getData('images_per_page');
    }

    /**
     * Retrieve how many products should be displayed
     *
     * @return int
     */
    public function getImagesCount()
    {
        if ($this->hasData('images_count')) {
            return $this->getData('images_count');
        }

        if (null === $this->getData('images_count')) {
            $this->setData('images_count', self::DEFAULT_POSTS_COUNT);
        }

        return $this->getData('images_count');
    }

    /**
     * Get widget block name
     *
     * @return string
     */
    public function getWidgetPagerBlockName()
    {
        $pageName = $this->getData('page_var_name');
        $pagerBlockName = 'widget.instagramfeedpro.list.pager';

        if (!$pageName) {
            return $pagerBlockName;
        }

        return $pagerBlockName . '.' . $pageName;
    }

    /**
     * @param IntsPost $post
     * @param array $data
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws LocalizedException
     */
    public function getMedia(IntsPost $post, $data = [])
    {
        $data['post'] = $post;

        return $this->getLayout()->createBlock(
            MediaType::class,
            '',
            [
                'data' => array_replace([
                    'post' => $post,
                    'widget' => $this->getData(),
                    'lazy_load_enabled' => $this->isLazyLoadEnabled(),
                ], $data),
            ]
        );
    }

    /**
     * @param IntsPost $post
     * @return string
     * @throws LocalizedException
     */
    public function getJSON(IntsPost $post)
    {
        $data = $post->getData();
        foreach (['code', 'store_id', 'is_active', 'ints_id'] as $key) {
            if (array_key_exists($key, $data)) {
                unset($data[$key]);
            }
        }
        foreach (['intspost_id', 'dimensions_width', 'dimensions_height', 'comments_count', 'like_count'] as $key) {
            if (array_key_exists($key, $data)) {
                $data[$key] = abs((int)$data[$key]);
            }
        }
        if (array_key_exists($data['owner'], $this->users)) {
            $data['owner_name'] = $this->users[$data['owner']];
        }
        $data['related'] = $post->isExistRelated();
        $data['full_id'] = $post->getFullId();
        $data['media_url'] = $post->getMediaUrl();
        $data['url'] = $post->getURL();
        $data['thumbnail_url'] = $post->getThumbnailUrl();
        $data['children'] = $post->getChildrenUrls();
        $format = $this->getDateFormat() ?: IntlDateFormatter::SHORT;
        $data['timestamp_orig'] = $post->getTimestamp();
        $data['timestamp'] = $this->formatDate($post->getTimestamp(), $format);
        /** @var IntsUser $owner */
        $owner = $post->getIntsUser();
        $data['owner_profile_picture'] = $owner ? $owner->getProfilePictureUrl() : null;

        return str_replace("'", "", (string)$this->json->serialize($data));
    }

    /**
     * @param array $args
     * @return string
     */
    public function getConfig($args = [])
    {
        $options = $this->helper->getModuleConfig('modal');
		$modalClasses = 'ox-instagram-modal';
		$temp = $this->helper->getSystemValue('olegnax_instagram_pro_appearance/modal/slider_nav_style');
		if($temp){
			$modalClasses .= ' ox-inst__nav-' . $temp;
		}
		$temp = $this->helper->getSystemValue('olegnax_instagram_pro_appearance/modal/slider_dots_style');
		if($temp){
			$modalClasses .= ' ox-inst__dots-' . $temp;
		}
		$lazy = $this->isLazyLoadEnabled();
        $config = [
			'relatedLayout' => $options['related_layout'],
            'showShare' => (bool)$options['show_share'],
            'showNextPrev' => (bool)$options['next_prev'],
            'showFollow' => (bool)$options['show_follow'],
            'showComments' => (bool)$options['show_comments'],
            'showLikes' => (bool)$options['show_likes'],
            'showCaption' => (bool)$options['show_caption'],
            'showDate' => (bool)$options['show_date'],
            'dialogOptions' => [
                'modalClass' => $modalClasses,
            ],
            'lazy' => $lazy,
            'lazy_placeholder' => $this->getViewFileUrl('Olegnax_InstagramFeedPro::images/preloader.svg'),
        ];

        return $this->json->serialize(array_replace($config, $args));
    }

    /**
     * @param string $key
     * @return array|mixed|null
     */
    public function getWidgetData($key)
    {
        $data = $this->getData($key);
        if ('' === $data && !in_array($key, [])) {
            $data = $this->helper->getSystemValue('olegnax_instagram_pro_appearance/feed/' . $key);
        }

        return $data;
    }

    /**
     * @param string $key
     * @param null $default
     * @return array|mixed|null
     */
    public function getDataWDefault($key = '', $default = null)
    {
        return $this->hasData($key)
            ? $this->getData($key)
            : $default;
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isVideoExist()
    {
        if ($this->getImagesCollection() && $this->getImagesCollection()->getSize()) {
            $items = $this->getImagesCollection()->getItems();
            /** @var IntsPost $item */
            foreach ($items as $item) {
                if (\Olegnax\InstagramFeedPro\Model\Config\Source\MediaType::VIDEO === $item->getMediaType()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->addData([
            'cache_lifetime' => 86400,
        ]);
        parent::_construct();
    }

    /**
     * @return Helper
     */
    protected function getHelper()
    {
        return $this->helper;
    }
    /**
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getHyvaCarouselJS()
    {
        $blockName = 'OXInstagramFeedPro_hyva-carousel-js';
        if (!$this->getLayout()->getBlock($blockName)) {
            return $this->getLayout()->createBlock(
                Template::class,
                $blockName
            )->setTemplate(static::TEMPLATE_CAROUSEL_JS)
            ->toHtml();
        }
        return '';
    }
        /**
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getHyvaWishlistJsTemplate()
    {
        $blockName = 'category.products.list.js.wishlist';
        $block = $this->getLayout()->getBlock($blockName);
        if (!$block) {
            return $this->getLayout()->createBlock(
                Template::class,
                $blockName
            )->setTemplate(static::TEMPLATE_HYVA_WISHLIST_JS)
            ->toHtml();
        } else{
            if (!$block->getData('is_rendered')) {
                return $block->setData('is_rendered', true)->toHtml();
            }
        }
        return '';
    }
    /**
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getHyvaModalTemplate()
    {
        $blockName = 'OXInstagramFeedPro_hyva-modal-template';
        if ($this->getShowModal() && !$this->getLayout()->getBlock($blockName)) {
            return $this->getLayout()->createBlock(
                Template::class,
                $blockName
            )->setTemplate(static::TEMPLATE_MODAL)
            ->setData('modalConfig', $this->getConfig())
            ->toHtml();
        }
        return '';
    }
    /**
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function createVideoJS()
    {
        $blockName = 'OXInstagramFeedPro_video-js';
        if ($this->isVideoExist()) {
            if (!$this->getLayout()->getBlock($blockName)) {
                return $this->getLayout()->createBlock(
                    Template::class,
                    $blockName
                )->setTemplate(static::TEMPLATE_JS)->toHtml();
            }
        }
        return '';
    }
    /**
     * @param array $options
     * @param bool $json
     * @return array|bool|false|string
     */
    public function getCarouselOptions($options = [], $json = true)
    {
        $autoplayTime = (int)$this->getAutoplayTime();
        if (!$autoplayTime || $autoplayTime < 500) {
            $autoplayTime = 500;
        }
        $options['loop'] = (bool)$this->getLoop();
        $options['dots'] = (bool)$this->getDots();
        $options['nav'] = (bool)$this->getNav();
        $options['items'] = (int)$this->getColumnsDesktop();
        $options['autoplay'] = (bool)$this->getAutoplay();
        $options['autoplayTimeout'] = $autoplayTime;
        $options['autoplayHoverPause'] = (bool)$this->getPauseOnHover();
        $options['lazyLoad'] = true;
        $options['rewind'] = (bool)$this->getRewind();
        $options['responsive'] = [
            '0' => [
                'items' => max(1, (int)$this->getColumnsMobile()),
            ],
            '640' => [
                'items' => max(1, (int)$this->getColumnsTablet()),
            ],
            '1025' => [
                'items' => max(1, (int)$this->getColumnsDesktop()),
            ]
        ];

        if ($json) {
            return $this->json->serialize($options);
        }

        return $options;
    }

    /**
     * @return bool
     */
    public function isLazyLoadEnabled()
    {
        return $this->helper->getConfig('olegnax_core_settings/general/lazyload') &&
            'exclude' != $this->getData('lazy_load');
    }
    /**
     * @param $widgetId
     * @param null $storeCode
     * @return string
     * @throws LocalizedException
     */
    public function getStyle($widgetId, $storeCode = null)
    {
        $content = $this->getGeneratedGridCss($widgetId, $storeCode);
        if (!empty($content)) {
            return $this->escapeCss->escapeCss($content);
        }
        return '';
    }
    /**
     * @param string $widgetId
     * @param int $storeCode
     * @return string
     * @throws LocalizedException
     */
    public function getGeneratedGridCss($widgetId, $storeCode = null)
    {
        return $this->getLayout()->createBlock(
            StyleBlock::class,
            '',
            [
                'data' => array_replace($this->getData(), [
                    'widgetId' => $widgetId,
                    'type' => Template::class,
                    'template' => 'Olegnax_InstagramFeedPro::styles_grid.phtml',
                ]),
            ]
        )
            ->toHtml();
    }
}
