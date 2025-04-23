<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 * @noinspection PhpDeprecationInspection
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class MediaType implements ArrayInterface
{
    const IMAGE = 'IMAGE';
    const VIDEO = 'VIDEO';
    const CAROUSEL_ALBUM = 'CAROUSEL_ALBUM';

    /**
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => static::IMAGE,
                'label' => __('Image'),
            ],
            [
                'value' => static::CAROUSEL_ALBUM,
                'label' => __('Album'),
            ],
            [
                'value' => static::VIDEO,
                'label' => __('Video'),
            ],
        ];
    }
}
