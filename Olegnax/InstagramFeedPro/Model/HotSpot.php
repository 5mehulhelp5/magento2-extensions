<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 * @noinspection PhpDeprecationInspection
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Model;

use Magento\Catalog\Model\Product;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;
use Olegnax\InstagramFeedPro\Api\Data\HotSpotInterface;
use Olegnax\InstagramFeedPro\Api\Data\HotSpotInterfaceFactory;
use Olegnax\InstagramFeedPro\Helper\Helper;
use Olegnax\InstagramFeedPro\Model\Config\Source\MarkerStyle;
use Olegnax\InstagramFeedPro\Model\ResourceModel\HotSpot\Collection;

/**
 * Class HotSpot
 * @method string getContent();
 * @method int getHotspotId();
 * @method string getMarkerStyle();
 * @method string getName();
 * @method string getHotspotWidth();
 * @method string getHotspotHeight();
 * @method string getHotspotTextFontsize();
 * @method string getHotspotTextFontweight();
 * @method string getHotspotTextLetterspacing();
 * @method string getHotspotColor();
 * @method string getHotspotShadowColor();
 * @method string getHotspotShadow();
 * @method string getHotspotBg();
 * @method string getHotspotRadius();
 * @method string getHotspotPulseColor();
 * @method string getHotspotCustomClass();
 * @method string getTooltipColor();
 * @method string getTooltipColorBackground();
 * @method string getTooltipWidth();
 * @method string getTooltipBorderRadius();
 * @method string getTooltipShadowColor();
 * @method Product|null getProduct();
 * @method void setContent($content);
 * @method void setHotspotId($hotspotId);
 * @method void setMarkerStyle($markerStyle);
 * @method void setName($title);
 * @method void setStatus($status);
 */
class HotSpot extends AbstractModel
{

    protected $hotspotDataFactory;

    protected $dataObjectHelper;

    protected $_eventPrefix = 'olegnax_instagramfeedpro_hotspot';
    /**
     * @var MarkerStyle
     */
    protected $markerStyle;
    /**
     * @var LayoutInterface
     */
    protected $_layout;
    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param HotSpotInterfaceFactory $hotspotDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceModel\HotSpot $resource
     * @param Helper $helper
     * @param Collection $resourceCollection
     * @param LayoutInterface $layout
     * @param MarkerStyle $markerStyle
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        HotSpotInterfaceFactory $hotspotDataFactory,
        DataObjectHelper $dataObjectHelper,
        ResourceModel\HotSpot $resource,
        Helper $helper,
        Collection $resourceCollection,
        LayoutInterface $layout,
        MarkerStyle $markerStyle,
        array $data = []
    ) {
        $this->hotspotDataFactory = $hotspotDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->_layout = $layout;
        $this->_helper = $helper;
        $this->markerStyle = $markerStyle;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve hotspot model with hotspot data
     * @return HotSpotInterface
     */
    public function getDataModel()
    {
        $hotspotData = $this->getData();

        $hotspotDataObject = $this->hotspotDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $hotspotDataObject,
            $hotspotData,
            HotSpotInterface::class
        );

        return $hotspotDataObject;
    }

    /**
     * @param array $args
     * @return string
     */
    public function getHtmlAttributes($args = [])
    {
        return $this->prepareAttributes(array_replace([
            'data-intspost-id' => $this->getIntsPostId(),
            'data-entity-id' => $this->getEntityId(),
            'data-image-index' => $this->getImageIndex(),
        ], $args));
    }

    /**
     * @return string
     */
    public function getUniqId()
    {
        return 'ox_insta_hotspot_' . substr(md5(microtime()), -5);
    }

    /**
     * @param array $attributes
     * @return string
     */
    private function prepareAttributes(array $attributes)
    {
        $attributes = array_filter($attributes);
        if (empty($attributes)) {
            return '';
        }
        $html = '';
        foreach ($attributes as $attributeName => $attributeValue) {
            $html .= sprintf(
                ' %s="%s"',
                $attributeName,
                str_replace(
                    '"',
                    '\"',
                    (string)$attributeValue
                )
            );
        }

        return $html;
    }

    /**
     * @return string
     */
    public function getStyle()
    {
        return $this->prepareStyle([
            'top' => $this->getPositionTop() ? $this->getPositionTop() . '%' : '',
            'left' => $this->getPositionLeft() ? $this->getPositionLeft() . '%' : '',
            'position' => 'absolute;',
        ]);
    }

    /**
     * @param array $style
     * @param string $separatorValue
     * @param string $separatorAttribute
     * @return string
     */
    private function prepareStyle(array $style, string $separatorValue = ':', string $separatorAttribute = ';')
    {
        $style = array_filter($style);
        if (empty($style)) {
            return '';
        }
        foreach ($style as $key => &$value) {
            $value = $key . $separatorValue . $value;
        }
        $style = implode($separatorAttribute, $style);

        return $style;
    }

    /**
     * @return float
     */
    public function getPositionTop()
    {
        return (float)$this->getData('position_top');
    }

    /**
     * @return int
     */
    public function getImageIndex()
    {
        return (int)$this->getData('image_index');
    }

    /**
     * @return float
     */
    public function getPositionLeft()
    {
        return (float)$this->getData('position_left');
    }

    /**
     * @return int
     */
    public function getIntsPostId()
    {
        return (int)$this->getData('intspost_id');
    }

    /**
     * @return bool
     */
    public function getStatus()
    {
        return '1' == $this->getData(Data\HotSpot::STATUS);
    }
    /**
     * @param string $key
     *
     * @return array|mixed|null
     */
    public function getHotSpotData($key)
    {
        $data = $this->getData($key);
        if (('' === $data || null === $data) && !in_array($key, [])) {
            $data = $this->_helper->getSystemValue('olegnax_instagram_pro_appearance/hotspots/' . $key);
        }

        return $data;
    }

    /**
     * @param string $key
     * @return array|mixed|null
     */
    public function getTooltipsData($key)
    {
        $data = $this->getData($key);
        if ('' === $data && !in_array($key, [])) {
            $data = $this->_helper->getSystemValue('olegnax_instagram_pro_appearance/tooltips/' . $key);
        }

        return $data;
    }

    /**
     * @param string $content
     * @return string
     */
    private function getBlockTemplateProcessor($content = '')
    {
        if (empty($content) || !is_string($content)) {
            $content = '';
        }
        $blockFilter = ObjectManager::getInstance()->get(FilterProvider::class)->getBlockFilter();
        return $blockFilter->filter(trim($content));
    }

    /**
     * @return string
     */
    public function getContentEncoded(){
        return $this->getBlockTemplateProcessor($this->getContent());
    }
}
