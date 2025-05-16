<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Block\Comment;

use Magento\Framework\View\Element\Template;

class JsInit extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_AdvancedReview::comments/js.phtml';

    /**
     * @return string
     */
    public function getUpdateUrl()
    {
        return $this->getUrl('amasty_advancedreview/ajax_comment/update');
    }

    /**
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('amasty_advancedreview/ajax_comment/add');
    }
}
