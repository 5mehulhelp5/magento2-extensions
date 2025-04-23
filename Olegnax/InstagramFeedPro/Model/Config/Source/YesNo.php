<?php /** @noinspection PhpDeprecationInspection */

/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class YesNo implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        $array = $this->toArray();
        foreach ($array as $key => $value) {
            $optionArray[] = ['value' => $key, 'label' => $value];
        }

        return $optionArray;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
			'' => __('Use Global Settings'),
            1 => __('Yes'),
            0 => __('No'),
        ];
    }
}
