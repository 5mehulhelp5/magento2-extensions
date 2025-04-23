<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface HotSpotInterface extends ExtensibleDataInterface
{

    const STATUS = 'status';
    const NAME = 'name';
    const MARKERSTYLE = 'marker_style';
	const HOTSPOTTEXTICON = 'hotspot_text_icon';
    const CONTENT = 'content';
    const HOTSPOT_ID = 'hotspot_id';
	
	const HOTSPOT_TEXT = 'hotspot_text';
	const HOTSPOT_WIDTH = 'hotspot_width';
	const HOTSPOT_HEIGHT = 'hotspot_height';
	const HOTSPOT_COLOR = 'hotspot_color';
	const HOTSPOT_BG = 'hotspot_bg';
	const HOTSPOT_PULSE = 'hotspot_pulse';
	const HOTSPOT_PULSE_COLOR = 'hotspot_pulse_color';
	const HOTSPOT_SHADOW = 'hotspot_shadow';
	const HOTSPOT_SHADOW_COLOR = 'hotspot_shadow_color';
	const HOTSPOT_RADIUS = 'hotspot_radius';
	const HOTSPOT_MOBILE = 'hotspot_mobile';
	const HOTSPOT_CUSTOM_CLASS = 'hotspot_custom_class';
	
	const TOOLTIP_WIDTH = 'tooltip_width';
	const TOOLTIP_BORDER_RADIUS = 'tooltip_border_radius';
	const TOOLTIP_TEXT_COLOR = 'tooltip_text_color';
	const TOOLTIP_BG_COLOR = 'tooltip_bg_color';
	const TOOLTIP_SHADOW_COLOR = 'tooltip_shadow_color';


    /**
     * Get hotspot_id
     * @return string|null
     */
    public function getHotspotId();

    /**
     * Set hotspot_id
     * @param string $hotspotId
     * @return HotSpotInterface
     */
    public function setHotspotId($hotspotId);

    /**
     * Get Name
     * @return string|null
     */
    public function getName();

    /**
     * Set Name
     * @param string $title
     * @return HotSpotInterface
     */
    public function setName($title);

    /**
     * Get Status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set Status
     * @param string $status
     * @return HotSpotInterface
     */
    public function setStatus($status);

    /**
     * Get MarkerStyle
     * @return string|null
     */
    public function getMarkerStyle();

    /**
     * Set MarkerStyle
     * @param string $markerStyle
     * @return HotSpotInterface
     */
    public function setMarkerStyle($markerStyle);
    /**
     * Get HotspotTextIcon
     * @return string|null
     */
    public function getHotspotTextIcon();

    /**
     * Set HotspotTextIcon
     * @param string $hotspotTextIcon
     * @return HotSpotInterface
     */
    public function setHotspotTextIcon($hotspotTextIcon);
    /**
     * Get Content
     * @return string|null
     */
    public function getContent();

    /**
     * Set Content
     * @param string $content
     * @return HotSpotInterface
     */
    public function setContent($content);
	
    /**
     * Get hotspot_text
     * @return string|null
     */
    public function getHotspotText();

    /**
     * Set hotspot_text
     * @param string $hotspotText
     * @return HotSpotInterface
     */
    public function setHotspotText($hotspotText);
	
    /**
     * Get hotspot_width
     * @return string|null
     */
    public function getHotspotWidth();

    /**
     * Set hotspot_width
     * @param string $hotspotWidth
     * @return HotSpotInterface
     */
    public function setHotspotWidth($hotspotWidth);
	
    /**
     * Get hotspot_height
     * @return string|null
     */
    public function getHotspotHeight();

    /**
     * Set hotspot_height
     * @param string $hotspotHeight
     * @return HotSpotInterface
     */
    public function setHotspotHeight($hotspotHeight);
	
    /**
     * Get hotspot_color
     * @return string|null
     */
    public function getHotspotColor();

    /**
     * Set hotspot_color
     * @param string $hotspotColor
     * @return HotSpotInterface
     */
    public function setHotspotColor($hotspotColor);
	
    /**
     * Get hotspot_bg
     * @return string|null
     */
    public function getHotspotBg();

    /**
     * Set hotspot_bg
     * @param string $hotspotBg
     * @return HotSpotInterface
     */
    public function setHotspotBg($hotspotBg);
	
    /**
     * Get hotspot_pulse
     * @return string|null
     */
    public function getHotspotPulse();

    /**
     * Set hotspot_pulse
     * @param string $hotspotPulse
     * @return HotSpotInterface
     */
    public function setHotspotPulse($hotspotPulse);
	
    /**
     * Get hotspot_pulse_color
     * @return string|null
     */
    public function getHotspotPulseColor();

    /**
     * Set hotspot_pulse_color
     * @param string $hotspotPulseColor
     * @return HotSpotInterface
     */
    public function setHotspotPulseColor($hotspotPulseColor);
	
    /**
     * Get hotspot_shadow
     * @return string|null
     */
    public function getHotspotShadow();

    /**
     * Set hotspot_shadow
     * @param string $hotspotShadow
     * @return HotSpotInterface
     */
    public function setHotspotShadow($hotspotShadow);
    /**
     * Get hotspot_shadow_color
     * @return string|null
     */
    public function getHotspotShadowColor();

    /**
     * Set hotspot_shadow_color
     * @param string $hotspotShadowColor
     * @return HotSpotInterface
     */
    public function setHotspotShadowColor($hotspotShadowColor);
    /**
     * Get hotspot_radius
     * @return string|null
     */
    public function getHotspotRadius();

    /**
     * Set hotspot_radius
     * @param string $hotspotRadius
     * @return HotSpotInterface
     */
    public function setHotspotRadius($hotspotRadius);
	
    /**
     * Get hotspot_mobile
     * @return string|null
     */
    public function getHotspotMobile();

    /**
     * Set hotspot_mobile
     * @param string $hotspotMobile
     * @return HotSpotInterface
     */
    public function setHotspotMobile($hotspotMobile);
	
    /**
     * Get hotspot_custom_class
     * @return string|null
     */
    public function getHotspotCustomClass();

    /**
     * Set hotspot_custom_class
     * @param string $hotspotCustomClass
     * @return HotSpotInterface
     */
    public function setHotspotCustomClass($hotspotCustomClass);
	
    /**
     * Get tooltip_width
     * @return string|null
     */
    public function getTooltipWidth();

    /**
     * Set tooltip_width
     * @param string $tooltipWidth
     * @return HotSpotInterface
     */
    public function setTooltipWidth($tooltipWidth);
	
    /**
     * Get tooltip_border_radius
     * @return string|null
     */
    public function getTooltipBorderRadius();

    /**
     * Set tooltip_border_radius
     * @param string $tooltipBorderRadius
     * @return HotSpotInterface
     */
    public function setTooltipBorderRadius($tooltipBorderRadius);
	
    /**
     * Get tooltip_text_color
     * @return string|null
     */
    public function getTooltipTextColor();

    /**
     * Set tooltip_text_color
     * @param string $tooltipTextColor
     * @return HotSpotInterface
     */
    public function setTooltipTextColor($tooltipTextColor);
    /**
     * Get tooltip_bg_color
     * @return string|null
     */
    public function getTooltipBgColor();

    /**
     * Set tooltip_bg_color
     * @param string $tooltipBgColor
     * @return HotSpotInterface
     */
    public function setTooltipBgColor($tooltipBgColor);
    /**
     * Get tooltip_shadow_color
     * @return string|null
     */
    public function getTooltipShadowColor();

    /**
     * Set tooltip_shadow_color
     * @param string $tooltipShadowColor
     * @return HotSpotInterface
     */
    public function setTooltipShadowColor($tooltipShadowColor);
}