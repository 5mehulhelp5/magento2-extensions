<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Data\Form\Element;

/**
 * Class Image - add multiple file upload
 */
class Image extends \Magento\Framework\Data\Form\Element\Image
{
    /**
     * @return string
     */
    public function getElementHtml()
    {
        $html = parent::getElementHtml();
        $html = str_replace('type="file"', 'type="file" multiple accept="image/*" ', $html);

        return $html;
    }
}
