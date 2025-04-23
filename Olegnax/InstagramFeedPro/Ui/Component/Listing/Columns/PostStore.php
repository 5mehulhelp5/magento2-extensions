<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Ui\Component\Listing\Columns;

use Magento\Store\Ui\Component\Listing\Column\Store;

class PostStore extends Store
{
    /**
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $item[$this->storeKey] = empty($item[$this->storeKey]) ? '0' : $item[$this->storeKey];

        if (!is_array($item[$this->storeKey])) {
            $item[$this->storeKey] = explode(',', $item[$this->storeKey]);
        }

        return parent::prepareItem($item);
    }
}
