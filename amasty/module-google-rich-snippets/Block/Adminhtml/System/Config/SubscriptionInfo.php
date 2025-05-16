<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Rich Snippets for Magento 2
 */

namespace Amasty\SeoRichData\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Module\Manager;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Framework\View\Helper\Js;

class SubscriptionInfo extends Fieldset
{
    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var string
     */
    private $notificationLink = 'https://amasty.com/amcustomer/account/products/?utm_source=extension&'
    . 'utm_medium=backend&utm_campaign=subscribe_snippets';

    public function __construct(
        Context $context,
        Session $authSession,
        Js $jsHelper,
        Manager $moduleManager,
        array $data = []
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data);
        $this->moduleManager = $moduleManager;
    }

    public function render(AbstractElement $element): string
    {
        if ($this->moduleManager->isEnabled('Amasty_SeoRichDataSubscriptionFunctionality')) {
            return '';
        }

        return $this->renderNotification();
    }

    public function renderNotification(): string
    {
        return <<<NOTIFICATION
            <div>
                <p class="message message-notice">
                    The functionality is available as part of an active product subscription or support subscription.
                    To upgrade and obtain functionality please follow the
                    <a target="_blank" href="{$this->notificationLink}">link</a>. Than you can find the
                    'amasty/module-seo-rich-data-subscription-functionality' package
                    for installation in composer suggest.
                </p>
            </div>
        NOTIFICATION;
    }
}
