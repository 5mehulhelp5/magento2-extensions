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

class ProductPagePos implements ArrayInterface
{
    /**
     * @var string
     */
    const STR_AFTER = '__after_';
    const STR_BEFORE = '__before_';
    const POS_1 = 'product.info.media' . self::STR_AFTER;
    const POS_6 = self::STR_AFTER . 'product.info.media';
	const POS_2 = 'product.info.main' . self::STR_AFTER;
    const POS_3 = 'product.info.main' . self::STR_AFTER . 'product.info.overview';
    const POS_5 = self::STR_AFTER . 'product.info.details';
	const POS_9 = self::STR_BEFORE . 'product.info.details';
	const POS_7 = 'main' . self::STR_BEFORE;
    const POS_8 = 'main' . self::STR_AFTER;

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
            ['value' => static::POS_1, 'label' => __('In Gallery Block')],
            ['value' => static::POS_6, 'label' => __('After Gallery Block')],
            ['value' => static::POS_2, 'label' => __('In Info Block, after all')],
            ['value' => static::POS_3, 'label' => __('In Info Block, after description')],
            ['value' => static::POS_5, 'label' => __('After Tabs')],
			['value' => static::POS_9, 'label' => __('Before Tabs')],
            ['value' => static::POS_7, 'label' => __('In content before all')],
            ['value' => static::POS_8, 'label' => __('In content after all')],
        ];
    }
}
