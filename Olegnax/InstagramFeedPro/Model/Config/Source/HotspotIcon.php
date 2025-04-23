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

class HotspotIcon implements ArrayInterface
{
    const TYPE_NONE = '';
	const TYPE_SMALL_PLUS	 = 'small-plus';
	const TYPE_BAG	 = 'bag-rounded-outline';
	const TYPE_CART	 = 'cart';
	const TYPE_CUSTOM	 = 'custom';

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
            ['value' => static::TYPE_SMALL_PLUS, 'label' => __('Small Plus')],
            ['value' => static::TYPE_BAG, 'label' => __('Bag Roudned, Outline')],
            ['value' => static::TYPE_CART, 'label' => __('Cart')],
            /*['value' => static::TYPE_CUSTOM, 'label' => __('Custom Image')],*/
        ];
    }
}
