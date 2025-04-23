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

class CartIconsStyle implements ArrayInterface
{
    const STYLE_DEFAULT = '';
	const STYLE_BAG_SHARP	 = 'bag-s';
	const STYLE_CART  = 'cart';
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
            ['value' => static::STYLE_DEFAULT, 'label' => __('Default (Bag, Rounded)')],
            ['value' => static::STYLE_BAG_SHARP, 'label' => __('Bag, Sharp')],
			['value' => static::STYLE_CART, 'label' => __('Cart')],
        ];
    }
}
