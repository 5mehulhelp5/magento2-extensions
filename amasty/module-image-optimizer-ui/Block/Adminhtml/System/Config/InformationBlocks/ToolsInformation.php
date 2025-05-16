<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer UI for Magento 2 (System)
 */

namespace Amasty\ImageOptimizerUi\Block\Adminhtml\System\Config\InformationBlocks;

use Amasty\ImageOptimizer\Model\Image\CheckTools;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Template;

class ToolsInformation extends Template
{
    /**
     * @var CheckTools
     */
    private $checkTools;

    /**
     * @var string
     */
    protected $_template = 'Amasty_ImageOptimizerUi::config/information/magento_imageoptimizer_notification.phtml';

    public function __construct(
        Template\Context $context,
        CheckTools $checkTools,
        array $data = []
    ) {
        $this->checkTools = $checkTools;
        parent::__construct($context, $data);
    }

    public function getNotificationText($tools): Phrase
    {
        return __(
            'For proper image optimization and module functionality, please ensure that you have the'
            . ' following libraries installed: %1.',
            implode(', ', $tools)
        );
    }

    public function getUnavailableTools(): array
    {
        return $this->checkTools->getUnavailableTools();
    }
}
