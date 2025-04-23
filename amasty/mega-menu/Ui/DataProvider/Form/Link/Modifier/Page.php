<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Ui\DataProvider\Form\Link\Modifier;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenu\Model\OptionSource\CmsPage;
use Amasty\MegaMenu\Model\OptionSource\UrlKey;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class Page implements ModifierInterface
{
    public function modifyData(array $data)
    {
        foreach ($data as $key => $linkData) {
            if ($linkData[ItemInterface::LINK_TYPE] == UrlKey::LANDING_PAGE) {
                $linkData['landing_page'] = $linkData[LinkInterface::PAGE_ID];
                $linkData[LinkInterface::PAGE_ID] = CmsPage::NO;
                $data[$key] = $linkData;
            }
        }

        return $data;
    }

    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
