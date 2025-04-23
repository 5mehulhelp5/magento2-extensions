<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\Checkout\Helper;

use Magento\Framework\App\Helper\AbstractHelper as MagentoAbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Colors
 *
 * Thanks to Dragos Badea
 * @source https://gist.github.com/bedeabza/10463089
 */
class Colors extends MagentoAbstractHelper
{
    const DEFAULT_CHECKOUT_BASE_COLOR = '#1f965e';
    const DEFAULT_HEADER_BASE_COLOR   = '#1f965e';
    const DEFAULT_ACCENT_BASE_COLOR   = '#1f965e';

    const XML_PATH_CHECKOUT_BASE_COLOR = 'mageworx_checkout/design/main_color';
    const XML_PATH_HEADER_BASE_COLOR   = 'mageworx_checkout/design/header_color';
    const XML_PATH_ACCENT_BASE_COLOR   = 'mageworx_checkout/design/accent_color';

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getHeaderBaseColor(int $storeId = null): string
    {
        $value = $this->scopeConfig->getValue(
            static::XML_PATH_HEADER_BASE_COLOR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (!$value) {
            return static::DEFAULT_HEADER_BASE_COLOR;
        }

        return $value;
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getCheckoutBaseColor(int $storeId = null): string
    {
        $value = $this->scopeConfig->getValue(
            static::XML_PATH_CHECKOUT_BASE_COLOR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (!$value) {
            return static::DEFAULT_CHECKOUT_BASE_COLOR;
        }

        return $value;
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getAccentBaseColor(int $storeId = null): string
    {
        $value = $this->scopeConfig->getValue(
            static::XML_PATH_ACCENT_BASE_COLOR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (!$value) {
            return static::DEFAULT_ACCENT_BASE_COLOR;
        }

        return $value;
    }

    /**
     * @param string $hex
     * @return float[]|int[]
     */
    public function hexToHsl(string $hex): array
    {
        $hex = str_replace('#', '', $hex);
        $hex = [$hex[0] . $hex[1], $hex[2] . $hex[3], $hex[4] . $hex[5]];
        $rgb = array_map(
            function ($part) {
                return hexdec($part) / 255;
            },
            $hex
        );

        $max = max($rgb);
        $min = min($rgb);

        $l = ($max + $min) / 2;

        if ($max == $min) {
            $h = $s = 0;
        } else {
            $diff = $max - $min;
            $s    = $l > 0.5 ? $diff / (2 - $max - $min) : $diff / ($max + $min);

            switch ($max) {
                case $rgb[0]:
                    $h = ($rgb[1] - $rgb[2]) / $diff + ($rgb[1] < $rgb[2] ? 6 : 0);
                    break;
                case $rgb[1]:
                    $h = ($rgb[2] - $rgb[0]) / $diff + 2;
                    break;
                case $rgb[2]:
                    $h = ($rgb[0] - $rgb[1]) / $diff + 4;
                    break;
            }

            $h /= 6;
        }

        return [(int)($h * 360), $s, $l];
    }

    /**
     * @param array $hsl
     * @return string
     */
    public function hslToHex(array $hsl): string
    {
        list($h, $s, $l) = $hsl;

        if ($s == 0) {
            $r = $g = $b = 1;
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;

            $r = $this->hue2rgb($p, $q, $h + 1 / 3);
            $g = $this->hue2rgb($p, $q, $h);
            $b = $this->hue2rgb($p, $q, $h - 1 / 3);
        }

        return $this->rgb2hex($r) . $this->rgb2hex($g) . $this->rgb2hex($b);
    }

    /**
     * @param float|int|string $p
     * @param float|int|string $q
     * @param float|int|string $t
     * @return float|int
     */
    public function hue2rgb($p, $q, $t)
    {
        if ($t < 0) {
            $t++;
        }
        if ($t > 1) {
            $t--;
        }
        if ($t < 1 / 6) {
            return $p + ($q - $p) * 6 * $t;
        }
        if ($t < 1 / 2) {
            return $q;
        }
        if ($t < 2 / 3) {
            return $p + ($q - $p) * (2 / 3 - $t) * 6;
        }

        return $p;
    }

    /**
     * @param float|int|string $rgb
     * @return string
     */
    public function rgb2hex($rgb)
    {
        return str_pad(dechex($rgb * 255), 2, '0', STR_PAD_LEFT);
    }
}
