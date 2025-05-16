<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Block\Adminhtml;

/**
 * Class Stock
 */
class Stock extends \Magento\Backend\Block\Widget\Grid\Container
{
    public function _construct()
    {
        parent::_construct();
        $this->_controller = 'adminhtml_stock';
        $this->_blockGroup = 'Amasty_Xnotif';
        $this->removeButton('add');
    }
}
