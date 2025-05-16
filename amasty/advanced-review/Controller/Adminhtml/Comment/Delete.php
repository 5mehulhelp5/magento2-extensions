<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Controller\Adminhtml\Comment;

use Amasty\AdvancedReview\Controller\Adminhtml\Comment as CommentController;

class Delete extends CommentController
{
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int) $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->getCommentRepository()->deleteById($id);
                $this->getMessageManager()->addSuccessMessage(__('You deleted the comment.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->getMessageManager()->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->getMessageManager()->addErrorMessage(__('We can\'t find a comment to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}
