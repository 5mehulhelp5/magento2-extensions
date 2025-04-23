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

class MarkerStyle implements ArrayInterface
{
    /**
     * @var string
     */
    const TYPE_DEFAULT = 'default';
    const TYPE_TAG45 = 'tag-45';
    const TYPE_TAG_HS = 'tag-hs';
    const TYPE_TAG_HT = 'tag-ht';
    const TYPE_BAG_F = 'bag-filled';
	const TYPE_CART_C = 'cart-circle';
    const TYPE_BAG_C = 'bag-circle';

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
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => static::TYPE_DEFAULT,
                'label' => __('Default'),
            ],
            [
                'value' => static::TYPE_TAG45,
                'label' => __('Tag Icon, 45 degree'),
            ],
            [
                'value' => static::TYPE_TAG_HT,
                'label' => __('Tag Shape, Horizontal'),
            ],
			[
                'value' => static::TYPE_TAG_HS,
                'label' => __('Square Tag Shape, Horizontal'),
            ],
            [
                'value' => static::TYPE_BAG_F,
                'label' => __('Bag Shape, Filled'),
            ],
			[
                'value' => static::TYPE_BAG_C,
                'label' => __('Outlined Bag Icon in Circle'),
            ],
			[
                'value' => static::TYPE_CART_C,
                'label' => __('Outlined Cart Icon in Circle'),
            ],
        ];
    }
}
