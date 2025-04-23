<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Plugin;

class SetDefaultShippingCountry
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    private $directoryHelper;

    /**
     * SetDefaultShippingCountry constructor.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Directory\Helper\Data $directoryHelper
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->directoryHelper = $directoryHelper;
    }

    /**
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface $subject
     * @param $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $method
     * @return array
     */
    public function beforeSet(
        \Magento\Quote\Api\PaymentMethodManagementInterface $subject,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $method
    ) {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId);
        if (!$quote->isVirtual()) {
            $address = $quote->getShippingAddress();
            // check if shipping address is set
            if ($address->getCountryId() === null) {
                $address->setCountryId($this->getDefaultCountryId($quote->getStoreId()));
            }
        }

        return [$cartId, $method];
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    private function getDefaultCountryId(int $storeId = null)
    {
        return $this->directoryHelper->getDefaultCountry($storeId);
    }
}
