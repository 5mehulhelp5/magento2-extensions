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

class ImageResize implements ArrayInterface
{
    const TYPE_NONE = 'none';
	const TYPE_MAXHEIGHT	 = 'maxheight';
	const TYPE_SQUARE	 = 'square';

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
            ['value' => static::TYPE_NONE, 'label' => __('None, Original Size')],
            ['value' => static::TYPE_MAXHEIGHT, 'label' => __('Max Height')],
            ['value' => static::TYPE_SQUARE, 'label' => __('Square')],
        ];
    }
}
