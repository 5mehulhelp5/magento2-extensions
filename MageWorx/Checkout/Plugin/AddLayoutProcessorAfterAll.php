<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Plugin;

use Magento\Framework\Exception\LocalizedException;

class AddLayoutProcessorAfterAll
{
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \MageWorx\Checkout\Block\Checkout\Onepage\LayoutProcessorFactory
     */
    private $layoutProcessorFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * AddLayoutProcessorAfterAll constructor.
     *
     * @param \MageWorx\Checkout\Block\Checkout\Onepage\LayoutProcessorFactory $layoutProcessorFactory
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     */
    public function __construct(
        \MageWorx\Checkout\Block\Checkout\Onepage\LayoutProcessorFactory $layoutProcessorFactory,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->layoutProcessorFactory = $layoutProcessorFactory;
        $this->serializer             = $serializer;
        $this->logger                 = $logger;
    }

    /**
     * @param \Magento\Checkout\Block\Onepage $subject
     * @param string $jsLayoutSerialized
     * @return bool|string
     */
    public function afterGetJsLayout(\Magento\Checkout\Block\Onepage $subject, string $jsLayoutSerialized)
    {
        $jsLayout = $this->serializer->unserialize($jsLayoutSerialized);

        try {
            /** @var \MageWorx\Checkout\Block\Checkout\Onepage\LayoutProcessor $mageworxCheckoutLayoutProcessor */
            $mageworxCheckoutLayoutProcessor = $this->layoutProcessorFactory->create();
            $jsLayout                        = $mageworxCheckoutLayoutProcessor->process($jsLayout);
        } catch (LocalizedException $e) {
            $this->logger->emergency($e->getLogMessage());
        } finally {
            $jsLayoutSerializedAndUpdated = $this->serializer->serialize($jsLayout);
        }

        return $jsLayoutSerializedAndUpdated;
    }
}
