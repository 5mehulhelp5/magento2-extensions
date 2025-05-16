<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */
namespace Amasty\Xnotif\Block\Adminhtml\Catalog\Product\Edit\Tab\Alerts\Renderer;

use Magento\Framework\DataObject;

/**
 * Class LastName
 */
class LastName extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    public function render(DataObject $row)
    {
        if (!$row->getEntityId()) {
            $row->setLastname(__('Guest'));
        }
        return $row->getLastname();
    }
}
