<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Api\Data\Menu;

interface LinkSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface[]
     */
    public function getItems();

    /**
     * @param \Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
