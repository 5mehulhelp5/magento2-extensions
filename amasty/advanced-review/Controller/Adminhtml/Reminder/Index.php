<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Controller\Adminhtml\Reminder;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Amasty\AdvancedReview\Controller\Adminhtml\AbstractReminder
{
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_AdvancedReview::reminder');
        $resultPage->getConfig()->getTitle()->prepend(__('Review Reminder'));
        $resultPage->addBreadcrumb(__('Reminder'), __('Reminder'));

        return $resultPage;
    }
}
