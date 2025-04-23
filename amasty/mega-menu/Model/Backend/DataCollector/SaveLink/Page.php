<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\Backend\DataCollector\SaveLink;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenu\Model\OptionSource\UrlKey;
use Amasty\MegaMenuLite\Model\Backend\SaveLink\DataCollectorInterface;

class Page implements DataCollectorInterface
{
    public function execute(array $data): array
    {
        // cms page id or landing page id saved into one column named page_id in db
        if (!$data[LinkInterface::PAGE_ID] && $data[ItemInterface::LINK_TYPE] == UrlKey::LANDING_PAGE) {
            $data[LinkInterface::PAGE_ID] = $data['landing_page'];
        }

        // unselect link type if link value not choosen
        if ($this->isLinkValueNotSelect($data)) {
            $data[ItemInterface::LINK_TYPE] = UrlKey::NO;
        }

        return $data;
    }

    private function isLinkValueNotSelect(array $data): bool
    {
        return in_array($data[ItemInterface::LINK_TYPE], [UrlKey::CMS_PAGE, UrlKey::LANDING_PAGE])
            && !$data[LinkInterface::PAGE_ID];
    }
}
