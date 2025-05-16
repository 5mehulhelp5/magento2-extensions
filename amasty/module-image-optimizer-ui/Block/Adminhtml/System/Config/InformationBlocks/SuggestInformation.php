<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer UI for Magento 2 (System)
 */

namespace Amasty\ImageOptimizerUi\Block\Adminhtml\System\Config\InformationBlocks;

use Magento\Framework\Module\Manager;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Template;

class SuggestInformation extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_ImageOptimizerUi::config/information/suggest_information.phtml';

    /**
     * @var string
     */
    private $suggestLink = 'https://amasty.com/docs/doku.php?id=magento_2:google_page_speed_optimizer&utm_source='
    . 'extension&utm_medium=backend&utm_campaign=suggest_gpso#additional_packages_provided_in_composer_suggestions';

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var array
     */
    private $suggestModules;

    public function __construct(
        Template\Context $context,
        Manager $moduleManager,
        array $data = [],
        array $suggestModules = []
    ) {
        $this->moduleManager = $moduleManager;
        $this->suggestModules = $suggestModules;
        parent::__construct($context, $data);
    }

    public function getNotificationText(): Phrase
    {
        return __(
            'Extra features may be provided by additional packages in the extension\'s \'suggest\' section.'
            . ' Please explore the available suggested packages ',
            $this->suggestLink
        );
    }

    public function getSuggestLink(): string
    {
        return $this->suggestLink;
    }

    public function isShowNotification(): bool
    {
        foreach ($this->suggestModules as $module) {
            if (!$this->moduleManager->isEnabled($module)) {
                return true;
            }
        }

        return false;
    }
}
