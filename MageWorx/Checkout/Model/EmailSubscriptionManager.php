<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Model;

use Exception;
use MageWorx\Checkout\Api\EmailSubscriptionManagerInterface;

class EmailSubscriptionManager implements EmailSubscriptionManagerInterface
{
    /**
     * @var \MageWorx\Checkout\Model\ResourceModel\CheckoutDummyData
     */
    private $checkoutDummyDataResource;

    /**
     * @var \MageWorx\Checkout\Model\CheckoutDummyDataFactory
     */
    private $checkoutDummyDataFactory;

    /**
     * @var \Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface
     */
    private $maskedQuoteIdToQuoteId;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * EmailSubscriptionManager constructor.
     *
     * @param \MageWorx\Checkout\Model\ResourceModel\CheckoutDummyData $checkoutDummyDataResource
     * @param \MageWorx\Checkout\Model\CheckoutDummyDataFactory $checkoutDummyDataFactory
     * @param \Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \MageWorx\Checkout\Model\ResourceModel\CheckoutDummyData $checkoutDummyDataResource,
        \MageWorx\Checkout\Model\CheckoutDummyDataFactory $checkoutDummyDataFactory,
        \Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->checkoutDummyDataResource = $checkoutDummyDataResource;
        $this->checkoutDummyDataFactory  = $checkoutDummyDataFactory;
        $this->maskedQuoteIdToQuoteId    = $maskedQuoteIdToQuoteId;
        $this->logger                    = $logger;
    }

    /**
     * @param string $mask
     * @return int|null
     */
    private function getQuoteIdByMask(string $mask): ?int
    {
        try {
            $id = $this->maskedQuoteIdToQuoteId->execute($mask);
        } catch (Exception $exception) {
            $this->logger->alert($exception);

            return null;
        }

        return $id;
    }

    /**
     * @inheritDoc
     */
    public function saveSubscriptionGuest(bool $status, string $cartId): bool
    {
        $quoteId = $this->getQuoteIdByMask($cartId);
        if (!$quoteId) {
            return false;
        }

        return $this->saveSubscription($status, $quoteId);
    }

    /**
     * @inheritDoc
     */
    public function saveSubscription(bool $status, int $cartId): bool
    {
        /** @var \MageWorx\Checkout\Model\CheckoutDummyData $model */
        $model = $this->checkoutDummyDataFactory->create();
        $this->checkoutDummyDataResource->load($model, $cartId, 'cart_id');
        $model->setData('email_subscription', $status);
        $model->setData('cart_id', $cartId);

        try {
            $this->checkoutDummyDataResource->save($model);
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage());
            $this->logger->critical($exception->getTraceAsString());

            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getEmailSubscriptionStatusByQuoteId(int $quoteId): bool
    {
        /** @var \MageWorx\Checkout\Model\CheckoutDummyData $model */
        $model = $this->checkoutDummyDataFactory->create();
        $this->checkoutDummyDataResource->load($model, $quoteId, 'cart_id');
        $subscriptionStatus = (bool)$model->getData('email_subscription');

        return $subscriptionStatus;
    }
}
