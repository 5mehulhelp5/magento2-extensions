<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Plugins\ConfigurableProduct\Block\Product\View\Type\Configurable;

use Amasty\Xnotif\Model\ConfigProvider;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable;

class ShowOptionsForOutofStockConfigurable
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var bool|null
     */
    private $tempSalableValue;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    public function beforeToHtml(Configurable $subject): void
    {
        $product = $subject->getProduct();

        if ($this->configProvider->isShowForOutOfStockConfigurable() && !$product->isSaleable()) {
            $this->tempSalableValue = $product->getData('salable');
            $product->setData('salable', true);
        }
    }

    /**
     * @param Configurable $subject
     * @param string $html
     * @return string
     */
    public function afterToHtml(Configurable $subject, $html)
    {
        if ($this->tempSalableValue !== null) {
            $subject->getProduct()->setData('salable', $this->tempSalableValue);
            $this->tempSalableValue = null;
        }

        return $html;
    }
}
