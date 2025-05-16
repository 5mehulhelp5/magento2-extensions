<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Block\Customer\Review;

class View extends \Magento\Review\Block\Customer\View
{
    /**
     * Customer view template name
     *
     * @var string
     */
    protected $_template = 'Amasty_AdvancedReview::customer/view.phtml';

    /**
     * @return string
     */
    public function getReviewAnswerHtml()
    {
        $review = $this->getReviewData();
        if ($this->getData('config')->isAllowAnswer() && $review->getAnswer()) {
            $html = $review->getAnswer();
        }

        return $html ?? '';
    }
}
