<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\Checkout\Block\Adminhtml\Config\Frontend;

class ColorPicker extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    public function _getElementHtml(
        \Magento\Framework\Data\Form\Element\AbstractElement $element
    ) {
        $element->setClass('colpicker');
        $element->addCustomAttribute('autocomplete', 'off');

        return parent::_getElementHtml($element);
    }
}
