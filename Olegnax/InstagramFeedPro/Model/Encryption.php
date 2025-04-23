<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */

namespace Olegnax\InstagramFeedPro\Model;

class Encryption
{
    /**
     * Encode.
     *
     * @param string $string String to encode.
     * @param string $key Encode key.
     *
     * @return string
     * @since 1.0.2 Removed key based encryption for better php support.
     *
     * @since 1.0.0
     * @noinspection PhpUnusedParameterInspection
     */
    public static function encode($string, $key)
    {
        return trim(self::safe_b64encode($string));
    }

    /**
     * Safe base 64 encode.
     *
     * @param string $string String to encode.
     *
     * @return string
     * @since 1.0.0
     *
     */
    public static function safe_b64encode($string)
    {
        $data = base64_encode($string);
        $data = str_replace(['+', '/', '='], ['-', '_', ''], $data);

        return $data;
    }

    /**
     * Decode.
     *
     * @param string $string String to decode.
     * @param string $key Decode key.
     *
     * @return string
     * @since 1.0.2 Removed key based encryption for better php support.
     *
     * @since 1.0.0
     * @noinspection PhpUnusedParameterInspection
     */
    public static function decode($string, $key)
    {
        return trim(self::safe_b64decode($string));
    }

    /**
     * Safe base 64 decode.
     *
     * @param string $string String to decode.
     *
     * @return string
     * @since 1.0.0
     *
     */
    public static function safe_b64decode($string)
    {
        $data = str_replace(['-', '_'], ['+', '/'], $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }

        return base64_decode($data);
    }
}
