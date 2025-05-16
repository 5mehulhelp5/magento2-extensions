<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Lazy Load for Magento 2 (System)
 */

namespace Amasty\LazyLoad\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Module\Manager;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

class PreloadImages extends Field
{
    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(
        Context $context,
        Manager $moduleManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleManager = $moduleManager;
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if (!$this->moduleManager->isEnabled('Amasty_PageSpeedOptimizer')) {
            $tooltip = __(
                'If enabled the specified number of images will be excluded ' .
                'from Lazy Load and will be loaded along with the page content.'
            )->render();
        } else {
            $tooltip = __(
                'If enabled the specified number of images will be excluded from Lazy Load and will be' .
                ' loaded along with the page content. <br/>For images preload request before content,' .
                ' please make sure that Server Push/Preload is enabled and Preloaded images option is selected in the' .
                ' Asset Types to Server Push/Preload setting (to configure proceed to ' .
                'Stores -> Configuration -> Amasty Extensions -> Google Page Speed Optimizer -> Server Push/Preload).'
            )->render();
        }
        $element->setTooltip($tooltip);

        return parent::render($element);
    }
}
