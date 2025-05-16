<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Controller\Adminhtml\Comment;

use Amasty\AdvancedReview\Controller\Adminhtml\Comment as CommentController;

class Index extends CommentController
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->getPageFactory()->create();
        $resultPage->setActiveMenu('Amasty_AdvancedReview::comments');
        $resultPage->getConfig()->getTitle()->prepend(__('Comments'));
        $resultPage->addBreadcrumb(__('Comments'), __('Comments'));

        return $resultPage;
    }
}
