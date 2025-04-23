<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Premium Base for Magento 2
 */

namespace Amasty\MegaMenuPremium\Model;

use Amasty\MegaMenu\Model\ConfigProvider;
use Amasty\MegaMenu\Model\Menu\Subcategory;
use Amasty\MegaMenu\Model\OptionSource\MobileTemplate;
use Amasty\MegaMenuLite\Model\Menu\Frontend\GetItemData;

class IsNeedMobileContent
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    public function execute(int $level): bool
    {
        switch ($level <=> Subcategory::TOP_LEVEL - GetItemData::LEVEL_DIFF) {
            case -1:
                $result = false;
                break;
            case 0:
                $result = true;
                break;
            case 1:
                $result = $this->configProvider->getMobileTemplateClass() === MobileTemplate::DRILL_DOWN;
                break;
        }

        return $result;
    }
}
