<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Block\Widget\ProductReviews;

class Form extends \Magento\Review\Block\Form
{
    /**
     * @return int
     */
    protected function getProductId()
    {
        return $this->getProduct()->getId();
    }

    /**
     * @return string
     */
    public function getJsLayout()
    {
        $this->jsLayout = $this->getData('jsLayout');

        return parent::getJsLayout();
    }

    /**
     * @return string|string[]|null
     */
    public function toHtml()
    {
        $html = parent::toHtml();
        $id = $this->getProduct()->getId();
        $html = preg_replace('/(id=\")(.*?)\"/', '$1$2-' . $id . '"', $html);
        $html = preg_replace('/(for=\")(.*?)\"/', '$1$2-' . $id . '"', $html);
        $html = str_replace('#review-form', '#review-form-' . $id, $html);

        return $html;
    }
}
