<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\Checkout\Plugin;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\ShippingMethodInterface;
use Magento\Quote\Api\GuestShipmentEstimationInterface;
use Psr\Log\LoggerInterface;

class SaveShippingAddressOnEachChangeByGuest
{
    /**
     * @var CartRepositoryInterface
     */
    protected CartRepositoryInterface $cartRepository;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param CartRepositoryInterface $cartRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        LoggerInterface         $logger
    ) {
        $this->cartRepository = $cartRepository;
        $this->logger         = $logger;
    }

    /**
     * After plugin for estimateByExtendedAddress
     *
     * @param GuestShipmentEstimationInterface $subject
     * @param ShippingMethodInterface[] $result
     * @param mixed $cartId
     * @param AddressInterface $address
     * @return ShippingMethodInterface[]
     */
    public function afterEstimateByExtendedAddress(
        GuestShipmentEstimationInterface $subject,
        array                            $result,
                                         $cartId,
        AddressInterface                 $address
    ): array {
        $quoteId = $address->getQuoteId();
        if ($quoteId) {
            try {
                $quote = $this->cartRepository->getActive($quoteId);
                // Save quote to update shipping address fields
                // It must prevent the missing address fields error with an external payment methods (like Braintree PayPal)
                $this->cartRepository->save($quote);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        return $result;
    }
}
