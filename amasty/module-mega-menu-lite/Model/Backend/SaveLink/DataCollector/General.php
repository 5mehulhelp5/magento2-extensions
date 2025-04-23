<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Backend\SaveLink\DataCollector;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenuLite\Model\Backend\SaveLink\DataCollectorInterface;
use Amasty\MegaMenuLite\Model\OptionSource\UrlKey;

class General implements DataCollectorInterface
{
    public function execute(array $data): array
    {
        if (isset($data[LinkInterface::ENTITY_ID])) {
            $data[LinkInterface::ENTITY_ID] = $data[LinkInterface::ENTITY_ID] ?: null;
        }

        // unselect link type if link value not choosen
        if ($this->isLinkValueNotSelect($data)) {
            $data[ItemInterface::LINK_TYPE] = UrlKey::NO;
        }

        if (!$this->isLinkSelected((int) $data[ItemInterface::LINK_TYPE])) {
            $data[ItemInterface::LINK] = '';
        }

        return $data;
    }

    private function isLinkValueNotSelect(array $data): bool
    {
        return $this->isLinkSelected((int) $data[ItemInterface::LINK_TYPE]) && !$data[ItemInterface::LINK];
    }

    private function isLinkSelected(int $type): bool
    {
        return in_array($type, [UrlKey::LINK, UrlKey::EXTERNAL_URL]);
    }
}
