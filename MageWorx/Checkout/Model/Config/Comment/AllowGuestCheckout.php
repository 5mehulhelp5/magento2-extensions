<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Model\Config\Comment;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Config\Model\Config\CommentInterface;

class AllowGuestCheckout extends AbstractBlock implements CommentInterface
{
    /**
     * Retrieve element comment by element value
     *
     * @param string $elementValue
     * @return string
     */
    public function getCommentText($elementValue)
    {
        $url = $this->_urlBuilder->getUrl('adminhtml/system_config/edit/section/checkout');

        $link    = '<a href="' . $url . '" target="_blank">Configuration - Sales - Checkout - Checkout Options</a>';
        $comment = __(
            'This setting can be enabled/disabled in %1 section.',
            $link
        );

        return $comment;
    }
}
