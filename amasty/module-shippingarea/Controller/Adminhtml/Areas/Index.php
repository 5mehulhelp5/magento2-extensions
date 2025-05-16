<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Areas for Magento 2 (System)
 */

namespace Amasty\ShippingArea\Controller\Adminhtml\Areas;

use Amasty\ShippingArea\Controller\Adminhtml\Areas;

class Index extends Areas
{
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_ShippingArea::shipping_area');
        $resultPage->getConfig()->getTitle()->prepend(__('Shipping Areas'));

        return $resultPage;
    }
}
