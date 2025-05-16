<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Controller\Adminhtml\Comment;

use Amasty\AdvancedReview\Api\Data\CommentInterface;
use Amasty\AdvancedReview\Model\Sources\CommentStatus;

class MassInactivate extends AbstractMassAction
{
    /**
     * @param CommentInterface $comment
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function itemAction($comment)
    {
        try {
            $comment->setStatus(CommentStatus::STATUS_REJECTED);
            $this->getRepository()->save($comment);
        } catch (\Exception $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
