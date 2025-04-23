<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Olegnax\InstagramFeedPro\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class RelatedWidget implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 1,
                'label' => __('Yes')
            ],
            [
                'value' => 0,
                'label' => __('No')
            ],
            [
                'value' => -1,
                'label' => __('Show all posts if there are no related posts')
            ],
        ];
    }

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
}
