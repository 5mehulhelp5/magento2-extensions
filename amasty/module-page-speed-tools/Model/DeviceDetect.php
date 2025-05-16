<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Model;

use Amasty\PageSpeedTools\Lib\MobileDetect;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\PageCache\Model\Config;

class DeviceDetect extends MobileDetect
{
    public const DESKTOP = 'desktop';
    public const TABLET = 'tablet';
    public const MOBILE = 'mobile';

    /**
     * @var string
     */
    private $webPBrowsersString = '/(Edg|Firefox|Chrome|Opera)/i';

    /**
     * @var string
     */
    private $avifBrowsersString = '/(?!.*Edg)(Firefox|Chrome|Opera)/i';

    /**
     * @var string
     */
    private $deviceType;

    /**
     * @var bool
     */
    private $isWebpSupport;

    /**
     * @var bool
     */
    private $isAvifSupport;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CookieManagerInterface $cookieManager,
        RequestInterface $request,
        array $headers = null,
        $userAgent = null
    ) {
        parent::__construct($headers, $userAgent);
        $this->cookieManager = $cookieManager;
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
    }

    public function getDeviceParams(): array
    {
        if ($this->deviceType === null && $this->isWebpSupport === null && $this->isAvifSupport === null) {
            $avifHeader = $this->request->getHeader('X-Amasty-Accept-Avif');
            $webHeader = $this->request->getHeader('X-Amasty-Accept-Webp');
            $deviceHeader = $this->request->getHeader('X-Amasty-Device');

            if (($avifHeader || $webHeader) && $deviceHeader) {
                $this->deviceType = $deviceHeader;
                $this->isAvifSupport = (bool)$avifHeader;
                $this->isWebpSupport = (bool)$webHeader;
            } elseif ($this->scopeConfig->getValue(Config::XML_PAGECACHE_TYPE) == Config::VARNISH
                && !$this->cookieManager->getCookie(Http::COOKIE_VARY_STRING)
            ) {
                /**
                 * Fallback to default device detect behavior when Varnish is not configured properly
                 * and X-Magento-Vary cookie does not exist.
                 */
                $this->deviceType = \Amasty\PageSpeedTools\Model\DeviceDetect::DESKTOP;
                $this->isAvifSupport = false;
                $this->isWebpSupport = false;
            } else {
                $this->deviceType = $this->detectDevice();
                $this->isWebpSupport = $this->detectIsUseWebp();
                $this->isAvifSupport = $this->detectIsUseAvif();
            }
        }

        return [$this->deviceType, $this->isWebpSupport, $this->isAvifSupport];
    }

    public function getDeviceType(): string
    {
        [$deviceType] = $this->getDeviceParams();

        return $deviceType;
    }

    public function isUseWebP(): bool
    {
        [, $isWebpSupport] = $this->getDeviceParams();

        return $isWebpSupport;
    }

    public function isUseAvif(): bool
    {
        [, , $isAvifSupport] = $this->getDeviceParams();

        return $isAvifSupport;
    }

    protected function detectDevice(): string
    {
        if ($this->isTablet()) {
            return self::TABLET;
        }
        if ($this->isMobile()) {
            return self::MOBILE;
        }

        return self::DESKTOP;
    }

    protected function detectIsUseWebp(): bool
    {
        $userAgent = $this->getUserAgent() ?? '';
        $range14To99 = '(?:1[4-9]|[2-9][0-9])';
        $range10To99 = '(?:1[0-9]|[2-9][0-9])';
        $range11To99 = '(?:1[1-9]|[2-9][0-9])';
        $iphonePart = "\biPhone OS $range14To99";
        $ipadPart = "\biPad; CPU OS $range14To99";
        $macosPart = "\bMac OS X $range11To99";
        $macintoshPart = "\bMacintosh; Intel Mac OS X $range10To99";
        $appleSafariWebpString = "/($iphonePart|$ipadPart|$macintoshPart|$macosPart).*Version\/$range14To99/i";

        return (bool)preg_match($appleSafariWebpString, $userAgent)
            || (bool)preg_match($this->webPBrowsersString, $userAgent);
    }

    protected function detectIsUseAvif(): bool
    {
        $userAgent = $this->getUserAgent() ?? '';
        $range160To99 = '(?:1[6-9].[0-9]|[2-9][0-9])';
        $range164To99 = '(?:1[6-9].[4-9]|1[7-9]|[2-9][0-9])';
        $iphonePart = "\biPhone OS $range160To99";
        $ipadPart = "\biPad; CPU OS $range160To99";
        $macintoshPart = "\bMacintosh; Intel Mac OS X";
        $macSafariAvifString = "/($macintoshPart).*Version\/$range164To99/i";
        $iosSafariAvifString = "/($iphonePart|$ipadPart).*Version\/$range160To99/i";

        return (bool)preg_match($this->avifBrowsersString, $userAgent)
            || (bool)preg_match($macSafariAvifString, $userAgent)
            || (bool)preg_match($iosSafariAvifString, $userAgent);
    }
}
