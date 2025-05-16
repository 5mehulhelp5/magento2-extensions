<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Block;

use Amasty\XnotifSubscriptionFunctionality\Model\ConfigProvider;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Restock extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_XnotifSubscriptionFunctionality::restock_alert.phtml';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Context $context,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configProvider = $configProvider;
    }

    public function _toHtml(): string
    {
        if (!$this->configProvider->isRestockAlertEnabled()) {
            return '';
        }

        return parent::_toHtml();
    }
}
