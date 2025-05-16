<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Block\Adminhtml\Settings;

use Magento\Framework\Data\Form\Element\AbstractElement;

class ClearMobileFolderCheckbox extends CommonCheckbox
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $element->setData('value', __("Mobile Images Folder"));
        $element->setData('class', "amoptimizer-checkbox");
        $element->setData('name', "mobile");

        return parent::_getElementHtml($element);
    }
}
