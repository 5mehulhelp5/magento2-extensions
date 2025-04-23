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

class SliderNavStyle implements ArrayInterface
{
    /**
     * @var string
     */
    const STYLE_DEFAULT = '';
    const STYLE_SQUARE = 'sharp';
    const STYLE_CIRCLE = 'circle';
	/**
     * Get options in "key-value" format
     *
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
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => static::STYLE_DEFAULT, 'label' => __('None, Use theme styles')],
            ['value' => static::STYLE_SQUARE, 'label' => __('Sharp')],
			['value' => static::STYLE_CIRCLE, 'label' => __('Circles')],
        ];
    }
}
