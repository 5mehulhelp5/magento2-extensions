<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Model\Config\Frontend;

use Magento\Store\Model\ScopeInterface;

class AllowGuestCheckout extends \Magento\Config\Block\System\Config\Form\Field
{
    const PARENT_VALUE_PATH = 'checkout/options/guest_checkout';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * NumberOfProducts constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve element HTML markup
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $value          = $this->scopeConfig->getValue(
            static::PARENT_VALUE_PATH,
            ScopeInterface::SCOPE_STORE
        );
        $valueFormatted = $value ? __('Yes') : __('No');

        return '<span>' . $valueFormatted . '</span>';
    }
}
