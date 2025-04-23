<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Block\Adminhtml\System\Config\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Color extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = $element->getElementHtml();
        $value = $element->getData('value');
        $html .= '<script type="text/javascript">
            require(["jquery","jquery/colorpicker/js/colorpicker"], function ($) {
                $(document).ready(function () {
                    var input = $("#' . $element->getHtmlId() . '");
                    input.css({"backgroundColor" : "' . $value . '"});
                    input.ColorPicker({
                        color: "' . $value . '",
                        onChange: function (hsb, hex, rgb) {
                            hex = hex.indexOf("gradient") !== -1 ? hex : "#" + hex;
                            input.css({"backgroundColor" : hex}).val(hex);
                        }
                    });
                });
            });
            </script>';

        return $html;
    }
}
