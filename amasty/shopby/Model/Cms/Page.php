<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Improved Layered Navigation Base for Magento 2
 */

namespace Amasty\Shopby\Model\Cms;

class Page extends \Magento\Framework\Model\AbstractModel
{
    public const VAR_SETTINGS = 'amshopby_settings';

    protected function _construct()
    {
        $this->_init(\Amasty\Shopby\Model\ResourceModel\Cms\Page::class);
    }
}
