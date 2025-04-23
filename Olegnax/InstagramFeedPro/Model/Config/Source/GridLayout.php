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

class GridLayout implements ArrayInterface
{
    const TYPE_GRID = '';
	const TYPE_GRID_FIRST_BIG	 = 'first-image';
	const TYPE_GRID_BIG	 = 'big-images';
    /**
     * @var array
     */
    protected $array;

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        if (empty($this->array)) {
            $this->array = [];
            foreach ($this->toOptionArray() as $item) {
                $this->array[$item['value']] = $item['label'];
            }
        }

        return $this->array;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => static::TYPE_GRID, 'label' => __('Default Grid')],
            ['value' => static::TYPE_GRID_FIRST_BIG, 'label' => __('First Image Big')],
			['value' => static::TYPE_GRID_BIG, 'label' => __('Every nth Image is Big')],
        ];
    }
}
