<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface HotSpotSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get HotSpot list.
     * @return HotSpotInterface[]
     */
    public function getItems();

    /**
     * Set Title list.
     * @param HotSpotInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

