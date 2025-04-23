<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Block\Adminhtml\Form\Field;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Html\Select;
use Magento\Payment\Helper\Data as PaymentHelper;

class Methods extends Select
{
    /**
     * Payment methods cache
     *
     * @var array
     */
    protected $paymentMethods;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * Methods constructor.
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param PaymentHelper $paymentHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        PaymentHelper $paymentHelper,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->paymentHelper = $paymentHelper;
        $this->scopeConfig   = $scopeConfig;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value): self
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->_options = $this->_getMethods();
        }

        return parent::_toHtml();
    }

    /**
     * Retrieve allowed shipping methods
     *
     * @return array
     */
    protected function _getMethods(): array
    {
        if ($this->paymentMethods === null) {
            $this->paymentMethods = [];
            $this->paymentMethods = $this->getPaymentMethodsList();
        }

        return $this->paymentMethods;
    }

    /**
     * Option array of all payment methods
     *
     * @return array
     */
    private function getPaymentMethodsList(): array
    {
        return $this->paymentHelper->getPaymentMethodList();
    }
}
