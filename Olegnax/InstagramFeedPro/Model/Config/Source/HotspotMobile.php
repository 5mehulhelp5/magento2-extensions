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

class HotspotMobile implements ArrayInterface
{
    const TYPE_DEFAULT = '';
	const TYPE_MIN	 = 'min';
	const TYPE_HIDE	 = 'hide';

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
            ['value' => static::TYPE_DEFAULT, 'label' => __('Same as on Desktop')],
            ['value' => static::TYPE_MIN, 'label' => __('Minimize')],
            ['value' => static::TYPE_HIDE, 'label' => __('Hide')],
        ];
    }
}
