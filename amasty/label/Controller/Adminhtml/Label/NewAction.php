<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Controller\Adminhtml\Label;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Forward;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

class NewAction extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = Edit::ADMIN_RESOURCE;

    /**
     * @return Forward
     */
    public function execute()
    {
        /** @var Forward $forward **/
        $forward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        $forward->setModule('amasty_label');
        $forward->setController('label');

        return $forward->forward('edit');
    }
}
