<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\UrlInterface;
use Magento\Quote\Api\ShippingMethodManagementInterface;
use MageWorx\Checkout\Api\CartManagerInterface;
use MageWorx\Checkout\Api\CheckoutConfigInterface;

class CheckoutConfigProvider implements ConfigProviderInterface
{
    /**
     * @var ShippingMethodManagementInterface
     */
    private $shippingMethodManagement;

    /**
     * @var CartManagerInterface
     */
    private $cartManager;

    /**
     * @var CheckoutConfigInterface
     */
    private $checkoutConfig;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * CheckoutConfigProvider constructor.
     *
     * @param ShippingMethodManagementInterface $shippingMethodManagement
     * @param CartManagerInterface $cartManager
     * @param CheckoutConfigInterface $checkoutConfig
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ShippingMethodManagementInterface $shippingMethodManagement,
        CartManagerInterface $cartManager,
        CheckoutConfigInterface $checkoutConfig,
        UrlInterface $urlBuilder
    ) {
        $this->shippingMethodManagement = $shippingMethodManagement;
        $this->cartManager              = $cartManager;
        $this->checkoutConfig           = $checkoutConfig;
        $this->urlBuilder               = $urlBuilder;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig(): array
    {
        $output = [
            'shippingMethods'               => $this->getShippingMethods(),
            'mageworxCheckoutUrl'           => $this->getCheckoutUrl(),
            'defaultShippingMethod'         => $this->checkoutConfig->getDefaultShippingMethod(),
            'defaultPaymentMethod'          => $this->checkoutConfig->getDefaultPaymentMethod(),
            'labels'                        => $this->checkoutConfig->getLabels(),
            'createAccountAvailable'        => $this->checkoutConfig->isCreateAccountEnabled(),
            'createAccountCheckedByDefault' => $this->checkoutConfig->isCreateAccountEnabled()
                ? $this->checkoutConfig->isCreateAccountCheckedByDefault()
                : false,
            'createAccountTitle'            => $this->checkoutConfig->getCreateAccountTitle(),
            'createAccountForced'           => $this->checkoutConfig->isForcedCreateAccountEnabled(),
            'minPasswordLength'             => $this->checkoutConfig->getMinPasswordLength(),
            'minCharacterSets'              => $this->checkoutConfig->getRequiredCharacterClassesNumber(),
            'useCustomShippingAddressTemplate' => $this->checkoutConfig->isUseCustomShippingAddressTemplate()
        ];

        return $output;
    }

    /**
     * @return array
     */
    private function getShippingMethods(): array
    {
        $methods = [];
        $cartId  = $this->detectCartId();

        try {
            if ($cartId) {
                $methodsList = $this->shippingMethodManagement->getList($cartId);
                foreach ($methodsList as $method) {
                    if ($method instanceof AbstractSimpleObject) {
                        $data      = $method->__toArray();
                        $methods[] = $data;
                    }
                }
            }
        } catch (NoSuchEntityException $noSuchEntityException) {
            return $methods;
        } catch (StateException $stateException) {
            return $methods;
        }

        return $methods;
    }

    /**
     * @return int|null
     */
    private function detectCartId(): ?int
    {
        return $this->cartManager->getCurrentCartId();
    }

    /**
     * Get one page checkout page url
     *
     * @return string
     */
    private function getCheckoutUrl(): string
    {
        return $this->getUrl('mageworx_checkout/onepage');
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return  string
     */
    public function getUrl($route = '', $params = []): string
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
