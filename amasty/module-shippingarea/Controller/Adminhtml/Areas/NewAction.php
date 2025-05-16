<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Areas for Magento 2 (System)
 */

namespace Amasty\ShippingArea\Controller\Adminhtml\Areas;

use Amasty\ShippingArea\Controller\Adminhtml\Areas;

class NewAction extends Areas
{
    public function execute()
    {
        $this->_forward('edit');
    }
}
