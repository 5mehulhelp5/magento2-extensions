<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Api\Data\Menu;

use Amasty\MegaMenuLite\Api\Data\Menu\LinkSearchResultsInterface as LinkSearchResultsInterfaceLite;

interface LinkSearchResultsInterface extends LinkSearchResultsInterfaceLite
{
    /**
     * @return \Amasty\MegaMenu\Api\Data\Menu\LinkInterface[]
     */
    public function getItems();

    /**
     * @param \Amasty\MegaMenu\Api\Data\Menu\LinkInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
