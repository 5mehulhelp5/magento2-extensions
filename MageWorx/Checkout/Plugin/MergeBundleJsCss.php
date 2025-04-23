<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Plugin;

use Magento\Framework\View\Asset\ConfigInterface;

class MergeBundleJsCss
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \MageWorx\Checkout\Api\CheckoutConfigInterface
     */
    private $checkoutConfig;

    /**
     * MergeBundleJsCss constructor.
     *
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \MageWorx\Checkout\Api\CheckoutConfigInterface $checkoutConfig
    ) {
        $this->request = $request;
        $this->checkoutConfig = $checkoutConfig;
    }

    /**
     * @param ConfigInterface $subject
     * @param $result
     * @return bool
     */
    public function afterIsMergeCssFiles(ConfigInterface $subject, $result)
    {
        if ($this->isMageWorxCheckoutPage()) {
            $result = $result || $this->checkoutConfig->isMergeJsCssEnabled();
        }

        return $result;
    }

    /**
     * @param ConfigInterface $subject
     * @param $result
     * @return bool
     */
    public function afterIsBundlingJsFiles(ConfigInterface $subject, $result)
    {
        if ($this->isMageWorxCheckoutPage()) {
            $result = $result || $this->checkoutConfig->isMergeJsCssEnabled();
        }

        return $result;
    }

    /**
     * @param ConfigInterface $subject
     * @param $result
     * @return bool
     */
    public function afterIsMergeJsFiles(ConfigInterface $subject, $result)
    {
        if ($this->isMageWorxCheckoutPage()) {
            $result = $result || $this->checkoutConfig->isMergeJsCssEnabled();
        }

        return $result;
    }

    /**
     * @return bool
     */
    private function isMageWorxCheckoutPage(): bool
    {
        return $this->request->getRouteName() == 'mageworx_checkout';
    }
}
