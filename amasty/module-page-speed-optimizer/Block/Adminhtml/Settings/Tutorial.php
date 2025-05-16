<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Page Speed Optimizer Base for Magento 2
 */

namespace Amasty\PageSpeedOptimizer\Block\Adminhtml\Settings;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Tutorial extends \Magento\Config\Block\System\Config\Form\Field
{
    public function _getElementHtml(AbstractElement $element): string
    {
        /** @var \Magento\Backend\Block\Template $block */
        $block = $this->getLayout()
            ->createBlock(\Magento\Backend\Block\Template::class)
            ->setTemplate('Amasty_PageSpeedOptimizer::tutorial.phtml');

        return $block->toHtml();
    }
}
