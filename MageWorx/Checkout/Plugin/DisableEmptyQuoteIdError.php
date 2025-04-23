<?php

namespace MageWorx\Checkout\Plugin;

use Magento\Framework\Exception\NoSuchEntityException;

class DisableEmptyQuoteIdError
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @param $subject
     * @param callable $proceed
     * @param ...$args
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[] An array of shipping methods
     */
    public function aroundEstimateByExtendedAddress(
        $subject,
        callable $proceed,
        ...$args
    ): array {
        try {
            $result = $proceed(...$args);
        } catch (NoSuchEntityException $noSuchEntityException) {
            $this->logger->critical(__('Next error was hidden from customer in plugin: %1', get_class($this)));
            $this->logger->critical($noSuchEntityException->getLogMessage());

            return [];
        }

        return $result;
    }
}
