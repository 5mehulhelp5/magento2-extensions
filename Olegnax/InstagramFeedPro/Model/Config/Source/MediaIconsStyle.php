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

class MediaIconsStyle implements ArrayInterface
{
    const STYLE_DEFAULT = '-f';
	const STYLE_SHARP_OUTLINE	 = '-o-s';
	const STYLE_SHARP_FILLED  = '-f-s';
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
            ['value' => static::STYLE_DEFAULT, 'label' => __('Default (Rounded, Filled)')],
            ['value' => static::STYLE_SHARP_OUTLINE, 'label' => __('Sharp Outlined')],
			['value' => static::STYLE_SHARP_FILLED, 'label' => __('Sharp Filled')],
        ];
    }
}
