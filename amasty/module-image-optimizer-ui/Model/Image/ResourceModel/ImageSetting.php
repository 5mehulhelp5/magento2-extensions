<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer UI for Magento 2 (System)
 */

namespace Amasty\ImageOptimizerUi\Model\Image\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ImageSetting extends AbstractDb
{
    public const TABLE_NAME = 'amasty_page_speed_optimizer_image_setting';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, \Amasty\ImageOptimizerUi\Model\Image\ImageSetting::IMAGE_SETTING_ID);
    }
}
