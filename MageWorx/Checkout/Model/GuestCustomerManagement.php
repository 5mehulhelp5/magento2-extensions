<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Model;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\Checkout\Api\GuestCustomerManagementInterface;

/**
 * Class GuestCustomerManagement
 *
 * Used to process with guest customer information (as API)
 */
class GuestCustomerManagement implements GuestCustomerManagementInterface
{
    /**
     * @var ResourceModel\CheckoutDummyData
     */
    private $checkoutDummyDataResource;

    /**
     * @var CheckoutDummyDataFactory
     */
    private $checkoutDummyDataFactory;

    /**
     * Encryptor.
     *
     * @var \Magento\Framework\Encryption\Encryptor
     */
    protected $encryptor;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * GuestCustomerManagement constructor.
     *
     * @param ResourceModel\CheckoutDummyData $checkoutDummyDataResource
     * @param CheckoutDummyDataFactory $checkoutDummyDataFactory
     * @param \Magento\Framework\Encryption\Encryptor $encryptor
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \MageWorx\Checkout\Model\ResourceModel\CheckoutDummyData $checkoutDummyDataResource,
        \MageWorx\Checkout\Model\CheckoutDummyDataFactory $checkoutDummyDataFactory,
        \Magento\Framework\Encryption\Encryptor $encryptor,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->checkoutDummyDataResource = $checkoutDummyDataResource;
        $this->checkoutDummyDataFactory = $checkoutDummyDataFactory;
        $this->encryptor = $encryptor;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function saveTempPassword(string $cartId, string $email, string $password): bool
    {
        /** @var \MageWorx\Checkout\Model\CheckoutDummyData $model */
        $model = $this->checkoutDummyDataFactory->create();
        $this->checkoutDummyDataResource->load($model, $cartId, 'cart_id');
        $passwordHash = $this->encryptor->getHash($password, true);
        $model->setData('data', $passwordHash);
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
     * Get password hash for cart
     *
     * @param $cartId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getTempPasswordHash($cartId): string
    {
        /** @var \MageWorx\Checkout\Model\CheckoutDummyData $model */
        $model = $this->checkoutDummyDataFactory->create();
        $this->checkoutDummyDataResource->load($model, $cartId, 'cart_id');
        if (!$model->getId() || !$model->getData('data')) {
            throw new NoSuchEntityException(__('There is no record for specified cart id'));
        }

        return $model->getData('data');
    }
}
