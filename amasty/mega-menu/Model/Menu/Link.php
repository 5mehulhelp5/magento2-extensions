<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\Menu;

use Amasty\MegaMenu\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenuLite\Model\Menu\Link as LinkLite;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Link as LinkResourceLite;

class Link extends LinkLite implements LinkInterface
{
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(LinkResourceLite::class);
    }

    public function getPageId(): int
    {
        return (int) $this->_getData(LinkInterface::PAGE_ID);
    }

    public function setPageId(int $pageId)
    {
        $this->setData(LinkInterface::PAGE_ID, $pageId);
    }
}
