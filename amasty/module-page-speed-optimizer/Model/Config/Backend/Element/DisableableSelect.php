<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Page Speed Optimizer Base for Magento 2
 */

namespace Amasty\PageSpeedOptimizer\Model\Config\Backend\Element;

use Magento\Framework\Data\Form\Element\Select;

class DisableableSelect extends Select
{
    /**
     * @param array $option
     * @param array $selected
     * @return string
     */
    protected function _optionToHtml($option, $selected): string
    {
        $html = parent::_optionToHtml($option, $selected);
        if ($option['disabled'] ?? false) {
            $html = preg_replace('/<option /i', '<option disabled ', $html);
        }

        return $html;
    }
}
