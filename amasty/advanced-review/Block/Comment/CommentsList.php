<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Block\Comment;

use Amasty\AdvancedReview\Api\Data\CommentInterface;
use Magento\Framework\View\Element\Template;

class CommentsList extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_AdvancedReview::comments/list.phtml';

    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return CommentInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getComments()
    {
        return $this->getParentBlock()->getComments();
    }
}
