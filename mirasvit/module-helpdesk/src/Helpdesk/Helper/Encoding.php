<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.3.6
 * @copyright Copyright (C) 2025 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Helper;

// @codingStandardsIgnoreFile

//https://github.com/neitanod/forceutf8/blob/master/src/ForceUTF8/Encoding.php
class Encoding extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected static $win1252ToUtf8 = [
        128 => "\xe2\x82\xac",

        130 => "\xe2\x80\x9a",
        131 => "\xc6\x92",
        132 => "\xe2\x80\x9e",
        133 => "\xe2\x80\xa6",
        134 => "\xe2\x80\xa0",
        135 => "\xe2\x80\xa1",
        136 => "\xcb\x86",
        137 => "\xe2\x80\xb0",
        138 => "\xc5\xa0",
        139 => "\xe2\x80\xb9",
        140 => "\xc5\x92",

        142 => "\xc5\xbd",

        145 => "\xe2\x80\x98",
        146 => "\xe2\x80\x99",
        147 => "\xe2\x80\x9c",
        148 => "\xe2\x80\x9d",
        149 => "\xe2\x80\xa2",
        150 => "\xe2\x80\x93",
        151 => "\xe2\x80\x94",
        152 => "\xcb\x9c",
        153 => "\xe2\x84\xa2",
        154 => "\xc5\xa1",
        155 => "\xe2\x80\xba",
        156 => "\xc5\x93",

        158 => "\xc5\xbe",
        159 => "\xc5\xb8",
    ];

    /**
     * @var array
     */
    protected static $brokenUtf8ToUtf8 = [
        "\xc2\x80" => "\xe2\x82\xac",

        "\xc2\x82" => "\xe2\x80\x9a",
        "\xc2\x83" => "\xc6\x92",
        "\xc2\x84" => "\xe2\x80\x9e",
        "\xc2\x85" => "\xe2\x80\xa6",
        "\xc2\x86" => "\xe2\x80\xa0",
        "\xc2\x87" => "\xe2\x80\xa1",
        "\xc2\x88" => "\xcb\x86",
        "\xc2\x89" => "\xe2\x80\xb0",
        "\xc2\x8a" => "\xc5\xa0",
        "\xc2\x8b" => "\xe2\x80\xb9",
        "\xc2\x8c" => "\xc5\x92",

        "\xc2\x8e" => "\xc5\xbd",

        "\xc2\x91" => "\xe2\x80\x98",
        "\xc2\x92" => "\xe2\x80\x99",
        "\xc2\x93" => "\xe2\x80\x9c",
        "\xc2\x94" => "\xe2\x80\x9d",
        "\xc2\x95" => "\xe2\x80\xa2",
        "\xc2\x96" => "\xe2\x80\x93",
        "\xc2\x97" => "\xe2\x80\x94",
        "\xc2\x98" => "\xcb\x9c",
        "\xc2\x99" => "\xe2\x84\xa2",
        "\xc2\x9a" => "\xc5\xa1",
        "\xc2\x9b" => "\xe2\x80\xba",
        "\xc2\x9c" => "\xc5\x93",

        "\xc2\x9e" => "\xc5\xbe",
        "\xc2\x9f" => "\xc5\xb8",
    ];

    /*protected static $utf8ToWin1252 = [
        "\xe2\x82\xac" => "\x80",

        "\xe2\x80\x9a" => "\x82",
        "\xc6\x92" => "\x83",
        "\xe2\x80\x9e" => "\x84",
        "\xe2\x80\xa6" => "\x85",
        "\xe2\x80\xa0" => "\x86",
        "\xe2\x80\xa1" => "\x87",
        "\xcb\x86" => "\x88",
        "\xe2\x80\xb0" => "\x89",
        "\xc5\xa0" => "\x8a",
        "\xe2\x80\xb9" => "\x8b",
        "\xc5\x92" => "\x8c",

        "\xc5\xbd" => "\x8e",

        "\xe2\x80\x98" => "\x91",
        "\xe2\x80\x99" => "\x92",
        "\xe2\x80\x9c" => "\x93",
        "\xe2\x80\x9d" => "\x94",
        "\xe2\x80\xa2" => "\x95",
        "\xe2\x80\x93" => "\x96",
        "\xe2\x80\x94" => "\x97",
        "\xcb\x9c" => "\x98",
        "\xe2\x84\xa2" => "\x99",
        "\xc5\xa1" => "\x9a",
        "\xe2\x80\xba" => "\x9b",
        "\xc5\x93" => "\x9c",

        "\xc5\xbe" => "\x9e",
        "\xc5\xb8" => "\x9f",
    ];*/

    /**
     * @param string $text
     *
     * @return array|string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public static function toUTF8($text)
    {
        /**
         * Function \ForceUTF8\Encoding::toUTF8.
         *
         * This function leaves UTF8 characters alone, while converting almost all non-UTF8 to UTF8.
         *
         * It assumes that the encoding of the original string is either Windows-1252 or ISO 8859-1.
         *
         * It may fail to convert characters to UTF-8 if they fall into one of these scenarios:
         *
         * 1) when any of these characters:   ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞß
         *    are followed by any of these:  ("group B")
         *                                    ¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶•¸¹º»¼½¾¿
         * For example:   %ABREPRESENT%C9%BB. «REPRESENTÉ»
         * The "«" (%AB) character will be converted, but the "É" followed by "»" (%C9%BB)
         * is also a valid unicode character, and will be left unchanged.
         *
         * 2) when any of these: àáâãäåæçèéêëìíîï  are followed by TWO chars from group B,
         * 3) when any of these: ðñòó  are followed by THREE chars from group B.
         *
         * @name toUTF8
         *
         * @param string $text Any string.
         *
         * @return string The same string, UTF8 encoded
         */
        if (is_array($text)) {
            foreach ($text as $k => $v) {
                $text[$k] = self::toUTF8($v);
            }

            return $text;
        } elseif (is_string($text)) {
            if (function_exists('mb_strlen') && ((int) ini_get('mbstring.func_overload')) & 2) {
                $max = mb_strlen($text, '8bit');
            } else {
                $max = strlen($text);
            }
            // @codingStandardsIgnoreStart
            $buf = '';
            for ($i = 0; $i < $max; ++$i) {
                $c1 = $text[$i];
                if ($c1 >= "\xc0") { //Should be converted to UTF8, if it's not UTF8 already
                    $c2 = $i + 1 >= $max ? "\x00" : $text[$i + 1];
                    $c3 = $i + 2 >= $max ? "\x00" : $text[$i + 2];
                    $c4 = $i + 3 >= $max ? "\x00" : $text[$i + 3];
                    if ($c1 >= "\xc0" & $c1 <= "\xdf") { //looks like 2 bytes UTF8
                        if ($c2 >= "\x80" && $c2 <= "\xbf") { //yeah, almost sure it's UTF8 already
                            $buf .= $c1.$c2;
                            ++$i;
                        } else { //not valid UTF8.  Convert it.
                            $cc1 = (chr(ord($c1) / 64) | "\xc0");
                            $cc2 = ($c1 & "\x3f") | "\x80";
                            $buf .= $cc1.$cc2;
                        }
                    } elseif ($c1 >= "\xe0" & $c1 <= "\xef") { //looks like 3 bytes UTF8
                        if ($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf") { //yeah, almost sure it's UTF8 already
                            $buf .= $c1.$c2.$c3;
                            $i = $i + 2;
                        } else { //not valid UTF8.  Convert it.
                            $cc1 = (chr(ord($c1) / 64) | "\xc0");
                            $cc2 = ($c1 & "\x3f") | "\x80";
                            $buf .= $cc1.$cc2;
                        }
                    } elseif ($c1 >= "\xf0" & $c1 <= "\xf7") { //looks like 4 bytes UTF8
                        if ($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf" && $c4 >= "\x80" && $c4 <= "\xbf") { //yeah, almost sure it's UTF8 already
                            $buf .= $c1.$c2.$c3.$c4;
                            $i = $i + 3;
                        } else { //not valid UTF8.  Convert it.
                            $cc1 = (chr(ord($c1) / 64) | "\xc0");
                            $cc2 = ($c1 & "\x3f") | "\x80";
                            $buf .= $cc1.$cc2;
                        }
                    } else { //doesn't look like UTF8, but should be converted
                        $cc1 = (chr(ord($c1) / 64) | "\xc0");
                        $cc2 = (($c1 & "\x3f") | "\x80");
                        $buf .= $cc1.$cc2;
                    }
                } elseif (($c1 & "\xc0") == "\x80") { // needs conversion
                    if (isset(self::$win1252ToUtf8[ord($c1)])) { //found in Windows-1252 special cases
                        $buf .= self::$win1252ToUtf8[ord($c1)];
                    } else {
                        $cc1 = (chr(ord($c1) / 64) | "\xc0");
                        $cc2 = (($c1 & "\x3f") | "\x80");
                        $buf .= $cc1.$cc2;
                    }
                } else { // it doesn't need conversion
                    $buf .= $c1;
                }
            }
            // @codingStandardsIgnoreEnd

            return $buf;
        } else {
            return $text;
        }
    }

    /**
     * @param string $text
     * @return array|bool|false|string
     */
    public static function toWin1252($text)
    {
        if (is_array($text)) {
            foreach ($text as $k => $v) {
                $text[$k] = self::toWin1252($v);
            }

            return $text;
        } elseif (is_string($text)) {
            return self::utf8Decode($text);
        } else {
            return $text;
        }
    }

    /**
     * @param string $text
     * @return array|bool|false|string
     */
    public static function toISO8859($text)
    {
        return self::toWin1252($text);
    }

    /**
     * @param string $text
     * @return array|bool|false|string
     */
    public static function toLatin1($text)
    {
        return self::toWin1252($text);
    }

    /**
     * @param string $text
     * @return array|string
     */
    public static function fixUTF8($text)
    {
        if (is_array($text)) {
            foreach ($text as $k => $v) {
                $text[$k] = self::fixUTF8($v);
            }

            return $text;
        }

        $last = '';
        while ($last != $text) {
            $last = $text;
            $text = self::toUTF8(self::utf8Decode($text));
        }
        $text = self::toUTF8(self::utf8Decode($text));

        return $text;
    }

    /**
     * @param string $text
     * @return string|string[]
     */
    public static function UTF8FixWin1252Chars($text)
    {
        // If you received an UTF-8 string that was converted from Windows-1252 as it was ISO8859-1
        // (ignoring Windows-1252 chars from 80 to 9F) use this function to fix it.
        // See: http://en.wikipedia.org/wiki/Windows-1252

        return str_replace(array_keys(self::$brokenUtf8ToUtf8), array_values(self::$brokenUtf8ToUtf8), $text);
    }

    /**
     * @param string $str
     * @return false|string
     */
    public static function removeBOM($str = '')
    {
        if (substr($str, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
            $str = substr($str, 3);
        }

        return $str;
    }

    /**
     * @param string $encodingLabel
     * @return mixed|string
     */
    public static function normalizeEncoding($encodingLabel)
    {
        $encoding = strtoupper($encodingLabel);
        $encoding = preg_replace('/[^a-zA-Z0-9\s]/', '', $encoding);
        $equivalences = [
            'ISO88591' => 'ISO-8859-1',
            'ISO8859' => 'ISO-8859-1',
            'ISO' => 'ISO-8859-1',
            'LATIN1' => 'ISO-8859-1',
            'LATIN' => 'ISO-8859-1',
            'UTF8' => 'UTF-8',
            'UTF' => 'UTF-8',
            'WIN1252' => 'ISO-8859-1',
            'WINDOWS1252' => 'ISO-8859-1',
        ];

        if (empty($equivalences[$encoding])) {
            return 'UTF-8';
        }

        return $equivalences[$encoding];
    }

    /**
     * @param string $encodingLabel
     * @param string $text
     * @return array|bool|false|string
     */
    public static function encode($encodingLabel, $text)
    {
        $encodingLabel = self::normalizeEncoding($encodingLabel);
        if ($encodingLabel == 'UTF-8') {
            /* @noinspection PhpUndefinedClassInspection */
            return self::toUTF8($text);
        }
        if ($encodingLabel == 'ISO-8859-1') {
            return self::toLatin1($text);
        }
    }

    /**
     * @param string $text
     * @return bool|false|string
     */
    protected static function utf8Decode($text)
    {
        // $o = utf8Decode(
        //   str_replace(array_keys(self::$utf8ToWin1252), array_values(self::$utf8ToWin1252), self::toUTF8($text))
        // );
        $o = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);

        return $o;
    }
}
