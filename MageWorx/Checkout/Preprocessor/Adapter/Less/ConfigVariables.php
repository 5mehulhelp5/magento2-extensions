<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\Checkout\Preprocessor\Adapter\Less;

use Magento\Framework\View\Asset\PreProcessor;

class ConfigVariables implements \Magento\Framework\View\Asset\PreProcessorInterface
{
    /**
     * @var \MageWorx\Checkout\Helper\Colors
     */
    protected $checkoutColorsConfig;

    /**
     * @param \MageWorx\Checkout\Helper\Colors $checkoutColorsConfig
     */
    public function __construct(
        \MageWorx\Checkout\Helper\Colors $checkoutColorsConfig
    ) {
        $this->checkoutColorsConfig = $checkoutColorsConfig;
    }

    /**
     * @inheritDoc
     */
    public function process(PreProcessor\Chain $chain): void
    {
        $asset = $chain->getAsset();
        if ($asset->getModule() !== 'MageWorx_Checkout') {
            return;
        }

        if ($asset->getFilePath() !== 'css/main.less') {
            return;
        }

        $accentBaseColor   = $this->checkoutColorsConfig->getAccentBaseColor();
        $checkoutBaseColor = $this->checkoutColorsConfig->getCheckoutBaseColor();
        $headerBaseColor   = $this->checkoutColorsConfig->getHeaderBaseColor();

        $variables = <<<CONTENT
@color-accent: $accentBaseColor;
@color-accent-checkout-button: $checkoutBaseColor;
@color-accent-header: $headerBaseColor;
\n
CONTENT;

        $newContent = $variables . $chain->getContent();

        $chain->setContent(
            $newContent
        );
    }
}
