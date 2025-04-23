<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Api\Data\Menu;

use Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface as LinkInterfaceLite;

interface LinkInterface extends LinkInterfaceLite
{
    public const PAGE_ID = 'page_id';

    /**
     * @return mixed
     */
    public function getPageId();

    /**
     * @param int $pageId
     *
     * @return void
     */
    public function setPageId(int $pageId);
}
