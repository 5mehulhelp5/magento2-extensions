<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Areas for Magento 2 (System)
 */

namespace Amasty\ShippingArea\Controller\Adminhtml;

abstract class Areas extends \Magento\Backend\App\Action
{
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_ShippingArea::shipping_area');
    }
}
