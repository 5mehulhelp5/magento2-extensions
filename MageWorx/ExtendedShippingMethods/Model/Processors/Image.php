<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ExtendedShippingMethods\Model\Processors;

/**
 * Class Image
 *
 * Add images to the shipping method
 */
class Image implements \MageWorx\ExtendedShippingMethods\Api\DataProcessorInterface
{
    /**
     * @var array
     */
    private $imagesConfig;

    /**
     * @var \MageWorx\ExtendedShippingMethods\Helper\Data
     */
    private $helper;

    /**
     * Image constructor.
     *
     * @param \MageWorx\ExtendedShippingMethods\Helper\Data $helper
     */
    public function __construct(
        \MageWorx\ExtendedShippingMethods\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @inheritDoc
     */
    public function processShippingMethodAsAnObject(\Magento\Quote\Api\Data\ShippingMethodInterface $method)
    {
        $methodCode = $method->getCarrierCode() . '_' . $method->getMethodCode();
        $image = $this->getImageByMethodCode($methodCode);

        /** @var \Magento\Quote\Api\Data\ShippingMethodExtensionInterface $extensionAttributes */
        $extensionAttributes = $method->getExtensionAttributes();
        $extensionAttributes->setCheckoutImage($image);
        $method->setExtensionAttributes($extensionAttributes);
    }

    /**
     * @inheritDoc
     */
    public function processShippingMethodAsAnArray(array &$method)
    {
        $methodCode = $method['carrier'] . '_' . $method['method'];
        $image = $this->getImageByMethodCode($methodCode);
        if (!$image) {
            return;
        }

        /** @var \Magento\Quote\Api\Data\ShippingMethodExtensionInterface $extensionAttributes */
        $extensionAttributes = $method['extension_attributes'] ?? [];
        $extensionAttributes['checkout_image'] = $image;
        $method['extension_attributes'] = $extensionAttributes;
    }

    /**
     * @param string $methodCode
     * @return string|null
     */
    private function getImageByMethodCode(string $methodCode): ?string
    {
        $imagesConfig = $this->getImagesConfig();
        $parts = explode('_', $methodCode);
        $carrier = reset($parts);

        if (!empty($imagesConfig[$methodCode]['image'])) {
            return $imagesConfig[$methodCode]['image'];
        } elseif (!empty($imagesConfig[$carrier]['image'])) {
            return $imagesConfig[$carrier]['image'];
        }

        return null;
    }

    /**
     * @return array
     */
    private function getImagesConfig(): array
    {
        if ($this->imagesConfig !== null) {
            return $this->imagesConfig;
        }

        $addIcon = $this->helper->getShippingMethodsIcon();

        if (!$addIcon) {
            return [];
        }
        
        $this->imagesConfig = $this->helper->getShippingMethodsConfiguration();

        return $this->imagesConfig;
    }
}
