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

class ImageHover implements ArrayInterface
{
    const TYPE_NONE = '';
	const TYPE_ZOOMOUT	 = 'zoomout';
	const TYPE_ZOOMIN	 = 'zoomin';

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
            ['value' => static::TYPE_NONE, 'label' => __('None')],
            ['value' => static::TYPE_ZOOMOUT, 'label' => __('Zoom Out')],
            ['value' => static::TYPE_ZOOMIN, 'label' => __('Zoom In')],
        ];
    }
}
