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

class ItemStyle implements ArrayInterface
{
    const LAYOUT_DEFAULT = '';
	const LAYOUT_OVERLAY	 = 'overlay';
	const LAYOUT_BELOW	 = 'below';
	const LAYOUT_BELOW2	 = 'below -ia-center';
	const LAYOUT_OVERLAY2	 = 'overlay -s-below -ia-center';
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
            ['value' => static::LAYOUT_DEFAULT, 'label' => __('Image Only')],
            ['value' => static::LAYOUT_OVERLAY, 'label' => __('Content Overlay Image on Hover')],
			['value' => static::LAYOUT_BELOW, 'label' => __('Content Below Image')],
			['value' => static::LAYOUT_BELOW2, 'label' => __('Content Below Image, Centered')],
			['value' => static::LAYOUT_OVERLAY2, 'label' => __('Content Overlay Image on Hover, Likes and Comments Below')],
        ];
    }
}
