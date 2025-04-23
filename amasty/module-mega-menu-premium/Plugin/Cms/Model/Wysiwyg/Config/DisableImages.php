<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Premium Base for Magento 2
 */

namespace Amasty\MegaMenuPremium\Plugin\Cms\Model\Wysiwyg\Config;

use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\DataObject;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class DisableImages
{
    public const AM_MEGAMENU_MOBILE_CONTENT = 'am_mega_menu_mobile_content';
    public const ADD_IMAGES_FIELD = 'add_images';

    public function afterGetConfig(
        Config $subject,
        DataObject $config
    ): DataObject {
        if ($config->getData(self::AM_MEGAMENU_MOBILE_CONTENT)) {
            $config->setData(self::ADD_IMAGES_FIELD, false);
        }

        return $config;
    }
}
