<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 * @noinspection PhpDeprecationInspection
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use Olegnax\InstagramFeedPro\Api\Data\HotSpotInterface;

class HotSpot extends AbstractExtensibleObject implements HotSpotInterface
{

    /**
     * Get hotspot_id
     * @return string|null
     */
    public function getHotspotId()
    {
        return $this->_get(self::HOTSPOT_ID);
    }

    /**
     * Set hotspot_id
     * @param string $hotspotId
     * @return HotSpotInterface
     */
    public function setHotspotId($hotspotId)
    {
        return $this->setData(self::HOTSPOT_ID, $hotspotId);
    }

    /**
     * Get Name
     * @return string|null
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Set Name
     * @param string $title
     * @return HotSpotInterface
     */
    public function setName($title)
    {
        return $this->setData(self::NAME, $title);
    }

    /**
     * Get Status
     * @return string|null
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set Status
     * @param string $status
     * @return HotSpotInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get MarkerStyle
     * @return string|null
     */
    public function getMarkerStyle()
    {
        return $this->_get(self::MARKERSTYLE);
    }

    /**
     * Set MarkerStyle
     * @param string $markerStyle
     * @return HotSpotInterface
     */
    public function setMarkerStyle($markerStyle)
    {
        return $this->setData(self::MARKERSTYLE, $markerStyle);
    }

    /**
     * Get HotspotTextIcon
     * @return string|null
     */
    public function getHotspotTextIcon()
    {
        return $this->_get(self::HOTSPOTTEXTICON);
    }

    /**
     * Set HotspotTextIcon
     * @param string $hotspotTextIcon
     * @return HotSpotInterface
     */
    public function setHotspotTextIcon($hotspotTextIcon)
    {
        return $this->setData(self::HOTSPOTTEXTICON, $hotspotTextIcon);
    }
    /**
     * Get Content
     * @return string|null
     */
    public function getContent()
    {
        return $this->_get(self::CONTENT);
    }

    /**
     * Set Content
     * @param string $content
     * @return HotSpotInterface
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }
		
    /**
     * Get hotspot_text
     * @return string|null
     */
    public function getHotspotText()
    {
        return $this->_get(self::HOTSPOT_TEXT);
    }

    /**
     * Set hotspot_text
     * @param string $hotspotText
     * @return HotSpotInterface
     */
    public function setHotspotText($hotspotText)
    {
        return $this->setData(self::HOTSPOT_TEXT, $hotspotText);
    }
    /**
     * Get hotspot_width
     * @return string|null
     */
    public function getHotspotWidth()
	{
        return $this->_get(self::HOTSPOT_WIDTH);
    }

    /**
     * Set hotspot_width
     * @param string $hotspotWidth
     * @return HotSpotInterface
     */
    public function setHotspotWidth($hotspotWidth)
	{
        return $this->setData(self::HOTSPOT_WIDTH, $hotspotWidth);
    }
	
    /**
     * Get hotspot_height
     * @return string|null
     */
    public function getHotspotHeight()
	{
        return $this->_get(self::HOTSPOT_HEIGHT);
    }
	

    /**
     * Set hotspot_height
     * @param string $hotspotHeight
     * @return HotSpotInterface
     */
    public function setHotspotHeight($hotspotHeight)
	{
        return $this->setData(self::HOTSPOT_HEIGHT, $hotspotHeight);
    }
	
    /**
     * Get hotspot_color
     * @return string|null
     */
    public function getHotspotColor()
	{
        return $this->_get(self::HOTSPOT_COLOR);
    }

    /**
     * Set hotspot_color
     * @param string $hotspotColor
     * @return HotSpotInterface
     */
    public function setHotspotColor($hotspotColor)
	{
        return $this->setData(self::HOTSPOT_COLOR, $hotspotColor);
    }
	
    /**
     * Get hotspot_bg
     * @return string|null
     */
    public function getHotspotBg()
	{
        return $this->_get(self::HOTSPOT_BG);
    }

    /**
     * Set hotspot_bg
     * @param string $hotspotBg
     * @return HotSpotInterface
     */
    public function setHotspotBg($hotspotBg)
	{
        return $this->setData(self::HOTSPOT_BG, $hotspotBg);
    }
	
    /**
     * Get hotspot_pulse
     * @return string|null
     */
    public function getHotspotPulse()
	{
        return $this->_get(self::HOTSPOT_PULSE);
    }

    /**
     * Set hotspot_pulse
     * @param string $hotspotPulse
     * @return HotSpotInterface
     */
    public function setHotspotPulse($hotspotPulse)
	{
        return $this->setData(self::HOTSPOT_PULSE, $hotspotPulse);
    }
	
    /**
     * Get hotspot_pulse_color
     * @return string|null
     */
    public function getHotspotPulseColor()
	{
        return $this->_get(self::HOTSPOT_PULSE_COLOR);
    }

    /**
     * Set hotspot_pulse_color
     * @param string $hotspotPulseColor
     * @return HotSpotInterface
     */
    public function setHotspotPulseColor($hotspotPulseColor)
	{
        return $this->setData(self::HOTSPOT_PULSE_COLOR, $hotspotPulseColor);
    }
	
    /**
     * Get hotspot_shadow
     * @return string|null
     */
    public function getHotspotShadow()
	{
        return $this->_get(self::HOTSPOT_SHADOW);
    }

    /**
     * Set hotspot_shadow
     * @param string $hotspotShadow
     * @return HotSpotInterface
     */
    public function setHotspotShadow($hotspotShadow)
	{
        return $this->setData(self::HOTSPOT_SHADOW, $hotspotShadow);
    }
    /**
     * Get hotspot_shadow_color
     * @return string|null
     */
    public function getHotspotShadowColor()
	{
        return $this->_get(self::HOTSPOT_SHADOW_COLOR);
    }

    /**
     * Set hotspot_shadow_color
     * @param string $hotspotShadowColor
     * @return HotSpotInterface
     */
    public function setHotspotShadowColor($hotspotShadowColor)
	{
        return $this->setData(self::HOTSPOT_SHADOW_COLOR, $hotspotShadowColor);
    }
    /**
     * Get hotspot_radius
     * @return string|null
     */
    public function getHotspotRadius()
	{
        return $this->_get(self::HOTSPOT_RADIUS);
    }

    /**
     * Set hotspot_radius
     * @param string $hotspotRadius
     * @return HotSpotInterface
     */
    public function setHotspotRadius($hotspotRadius)
	{
        return $this->setData(self::HOTSPOT_RADIUS, $hotspotRadius);
    }
	
    /**
     * Get hotspot_mobile
     * @return string|null
     */
    public function getHotspotMobile()
	{
        return $this->_get(self::HOTSPOT_MOBILE);
    }

    /**
     * Set hotspot_mobile
     * @param string $hotspotMobile
     * @return HotSpotInterface
     */
    public function setHotspotMobile($hotspotMobile)
	{
        return $this->setData(self::HOTSPOT_MOBILE, $hotspotMobile);
    }
	
    /**
     * Get hotspot_custom_class
     * @return string|null
     */
    public function getHotspotCustomClass()
	{
        return $this->_get(self::HOTSPOT_CUSTOM_CLASS);
    }

    /**
     * Set hotspot_custom_class
     * @param string $hotspotCustomClass
     * @return HotSpotInterface
     */
    public function setHotspotCustomClass($hotspotCustomClass)
	{
        return $this->setData(self::HOTSPOT_CUSTOM_CLASS, $hotspotCustomClass);
    }
	
    /**
     * Get tooltip_width
     * @return string|null
     */
    public function getTooltipWidth()
	{
        return $this->_get(self::TOOLTIP_WIDTH);
    }

    /**
     * Set tooltip_width
     * @param string $tooltipWidth
     * @return HotSpotInterface
     */
    public function setTooltipWidth($tooltipWidth)
	{
        return $this->setData(self::TOOLTIP_WIDTH, $tooltipWidth);
    }
	
    /**
     * Get tooltip_border_radius
     * @return string|null
     */
    public function getTooltipBorderRadius()
	{
        return $this->_get(self::TOOLTIP_BORDER_RADIUS);
    }

    /**
     * Set tooltip_border_radius
     * @param string $tooltipBorderRadius
     * @return HotSpotInterface
     */
    public function setTooltipBorderRadius($tooltipBorderRadius)
	{
        return $this->setData(self::TOOLTIP_BORDER_RADIUS, $tooltipBorderRadius);
    }
	
    /**
     * Get tooltip_text_color
     * @return string|null
     */
    public function getTooltipTextColor()
	{
        return $this->_get(self::TOOLTIP_TEXT_COLOR);
    }

    /**
     * Set tooltip_text_color
     * @param string $tooltipTextColor
     * @return HotSpotInterface
     */
    public function setTooltipTextColor($tooltipTextColor)
	{
        return $this->setData(self::TOOLTIP_TEXT_COLOR, $tooltipTextColor);
    }
    /**
     * Get tooltip_bg_color
     * @return string|null
     */
    public function getTooltipBgColor()
	{
        return $this->_get(self::TOOLTIP_BG_COLOR);
    }

    /**
     * Set tooltip_bg_color
     * @param string $tooltipBgColor
     * @return HotSpotInterface
     */
    public function setTooltipBgColor($tooltipBgColor)
	{
        return $this->setData(self::TOOLTIP_BG_COLOR, $tooltipBgColor);
    }
    /**
     * Get tooltip_shadow_color
     * @return string|null
     */
    public function getTooltipShadowColor()
	{
        return $this->_get(self::TOOLTIP_SHADOW_COLOR);
    }

    /**
     * Set tooltip_shadow_color
     * @param string $tooltipShadowColor
     * @return HotSpotInterface
     */
    public function setTooltipShadowColor($tooltipShadowColor)
	{
        return $this->setData(self::TOOLTIP_SHADOW_COLOR, $tooltipShadowColor);
    }
}

