<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Controller\Adminhtml\Comment;

use Amasty\AdvancedReview\Api\Data\CommentInterface;
use Amasty\AdvancedReview\Model\Sources\CommentStatus;
use Magento\Ui\Component\MassAction\Filter;

class MassActivate extends AbstractMassAction
{
    /**
     * @param CommentInterface $comment
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    protected function itemAction($comment)
    {
        $comment->setStatus(CommentStatus::STATUS_APPROVED);
        $this->getRepository()->save($comment);

        $this->getCommentNotification()->sendMessage($comment);

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
