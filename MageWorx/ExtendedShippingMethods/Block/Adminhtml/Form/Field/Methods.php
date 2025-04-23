<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ExtendedShippingMethods\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;
use Magento\Shipping\Model\Config as ShippingConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Methods extends Select
{
    /**
     * @var ShippingConfig
     */
    protected $shippingMethodsConfig;

    /**
     * Shipping methods cache
     *
     * @var array
     */
    protected $shippingMethods;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param ShippingConfig $shippingConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        ShippingConfig $shippingConfig,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->shippingMethodsConfig = $shippingConfig;
        $this->scopeConfig           = $scopeConfig;
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
        if ($this->shippingMethods === null) {
            $this->shippingMethods = [];
            $this->shippingMethods = $this->getShippingMethodsList(false);
        }

        return $this->shippingMethods;
    }

    /**
     * Option array of all shipping methods
     *
     * @param bool $isActiveOnlyFlag
     *
     * @return array
     */
    private function getShippingMethodsList(bool $isActiveOnlyFlag = false): array
    {
        $methods  = [];
        $carriers = $this->shippingMethodsConfig->getAllCarriers();
        foreach ($carriers as $carrierCode => $carrierModel) {
            if (!$carrierModel->isActive() && (bool)$isActiveOnlyFlag === true) {
                continue;
            }
            $carrierMethods = $carrierModel->getAllowedMethods();
            if (!$carrierMethods || !is_array($carrierMethods)) {
                continue;
            }
            $carrierTitle          = $this->scopeConfig->getValue(
                'carriers/' . $carrierCode . '/title',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $methods[$carrierCode] = ['label' => $carrierTitle, 'value' => []];

            $methods[$carrierCode]['value'][] = [
                'value' => $carrierCode,
                'label' => '[' . $carrierCode . '] ' . __('For All Methods'),
            ];

            foreach ($carrierMethods as $methodCode => $methodTitle) {
                if (is_array($methodTitle)) {
                    continue;
                }

                $methods[$carrierCode]['value'][] = [
                    'value' => $carrierCode . '_' . $methodCode,
                    'label' => '[' . $carrierCode . '] ' .
                        ($methodTitle ? $methodTitle : $methodCode),
                ];
            }
        }

        return $methods;
    }
}
