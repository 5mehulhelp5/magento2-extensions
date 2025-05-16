<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Block\Adminhtml\System\Config;

use Magento\Framework\Module\Manager;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Template;

class CommonRulesNotification extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_Shiprules::config/information/common_rules_notification.phtml';

    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(
        Template\Context $context,
        Manager $moduleManager,
        array $data = []
    ) {
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $data);
    }

    public function getNotificationText(): Phrase
    {
        return __(
            'Enable the system-common-rules module for the extension to function correctly. ' .
            'Please run the following command in SSH: composer require amasty/system-common-rules.'
        );
    }

    public function shouldShowNotification(): bool
    {
        return !$this->moduleManager->isEnabled('Amasty_CommonRules');
    }
}
