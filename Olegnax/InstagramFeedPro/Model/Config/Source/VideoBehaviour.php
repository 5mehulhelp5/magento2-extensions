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

class VideoBehaviour implements ArrayInterface
{
    const NONE = '';
    const HOVER = 'hover';
    const CLICK = 'click';

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
                'value' => static::NONE,
                'label' => __('None. Don\'t play in grid'),
            ],
            [
                'value' => static::HOVER,
                'label' => __('Play on Hover'),
            ],
            [
                'value' => static::CLICK,
                'label' => __('Play on Click'),
            ],
        ];
    }
}
