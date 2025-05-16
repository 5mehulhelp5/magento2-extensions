<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Controller\Adminhtml\Comment;

use Amasty\AdvancedReview\Api\Data\CommentInterface;
use Amasty\AdvancedReview\Controller\Adminhtml\Comment as CommentController;
use Amasty\AdvancedReview\Model\Comment;
use Amasty\AdvancedReview\Model\RegistryConstants;
use Magento\Framework\Exception\LocalizedException;

class Save extends CommentController
{
    public function execute()
    {
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                $model = $this->getCommentModel();
                $model->addData($data);
                $this->getCommentRepository()->save($model);

                $this->messageManager->addSuccessMessage(__('Comment has been saved.'));

                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect(
                        '*/*/edit',
                        [CommentInterface::ID => $model->getId(), '_current' => true]
                    );
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->getDataPersistor()->set(RegistryConstants::COMMENT_DATA, $data);
                if ($commentId = (int) $this->getRequest()->getParam(CommentInterface::ID)) {
                    return $this->_redirect('*/*/edit', [CommentInterface::ID => $commentId]);
                }

                return $this->_redirect('*/*/');
            }
        }
        return $this->_redirect('*/*/');
    }

    /**
     * @return CommentInterface|Comment
     * @throws LocalizedException
     */
    protected function getCommentModel()
    {
        /** @var CommentInterface $model */
        $model = $this->getCommentRepository()->getComment();

        if ($commentId = (int) $this->getRequest()->getParam(CommentInterface::ID)) {
            $model = $this->getCommentRepository()->getById($commentId);
            if ($commentId != $model->getId()) {
                throw new LocalizedException(__('The wrong item is specified.'));
            }
        }

        return $model;
    }
}
