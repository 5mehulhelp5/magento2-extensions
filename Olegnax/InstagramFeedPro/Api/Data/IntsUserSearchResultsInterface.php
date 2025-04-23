<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface IntsUserSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get IntsUser list.
     * @return IntsUserInterface[]
     */
    public function getItems();

    /**
     * Set username list.
     * @param IntsUserInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
