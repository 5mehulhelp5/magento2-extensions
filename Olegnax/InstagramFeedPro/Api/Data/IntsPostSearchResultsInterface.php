<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface IntsPostSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get IntsPost list.
     * @return IntsPostInterface[]
     */
    public function getItems();

    /**
     * Set store_id list.
     * @param IntsPostInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
