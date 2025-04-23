<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Observer;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\QuoteIdMask;
use MageWorx\Checkout\Api\GuestCustomerManagementInterface;
use MageWorx\Checkout\Helper\CheckoutConfig;

class CustomerProcessAfterPlaceOrder implements ObserverInterface
{
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    private $orderFactory;

    /**
     * @var \Magento\Sales\Api\OrderCustomerManagementInterface
     */
    private $orderCustomerService;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var GuestCustomerManagementInterface
     */
    private $guestCustomerManagement;

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    private $addressFactory;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \MageWorx\Checkout\Api\EmailSubscriptionManagerInterface
     */
    private $emailSubscriptionManager;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @var CheckoutConfig
     */
    private $checkoutConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * CustomerProcessAfterPlaceOrder constructor.
     *
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Sales\Api\OrderCustomerManagementInterface $orderCustomerService
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param GuestCustomerManagementInterface $guestCustomerManagement
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param AddressRepositoryInterface $addressRepository
     * @param \MageWorx\Checkout\Api\EmailSubscriptionManagerInterface $emailSubscriptionManager
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param CheckoutConfig $checkoutConfig
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Api\OrderCustomerManagementInterface $orderCustomerService,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \MageWorx\Checkout\Api\GuestCustomerManagementInterface $guestCustomerManagement,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \MageWorx\Checkout\Api\EmailSubscriptionManagerInterface $emailSubscriptionManager,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \MageWorx\Checkout\Helper\CheckoutConfig $checkoutConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->orderFactory             = $orderFactory;
        $this->orderCustomerService     = $orderCustomerService;
        $this->customerFactory          = $customerFactory;
        $this->orderRepository          = $orderRepository;
        $this->storeManager             = $storeManager;
        $this->customerRepository       = $customerRepository;
        $this->customerRegistry         = $customerRegistry;
        $this->guestCustomerManagement  = $guestCustomerManagement;
        $this->quoteIdMaskFactory       = $quoteIdMaskFactory;
        $this->addressFactory           = $addressFactory;
        $this->addressRepository        = $addressRepository;
        $this->emailSubscriptionManager = $emailSubscriptionManager;
        $this->subscriberFactory        = $subscriberFactory;
        $this->messageManager           = $messageManager;
        $this->checkoutConfig           = $checkoutConfig;
        $this->customerSession          = $customerSession;
        $this->logger                   = $logger;
    }

    /**
     * Register customer if flag set
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();

        try {
            if (count($orderIds)) {
                $orderId = reset($orderIds);
                /** @var \Magento\Sales\Model\Order $order */
                $order = $this->orderRepository->get($orderId);

                $customer = $this->customerFactory->create();
                $customer->setWebsiteId($this->storeManager->getStore()->getWebsiteId());
                $customer->loadByEmail($order->getCustomerEmail());

                $cartId = $order->getQuoteId();

                if ($order->getId()) {
                    try {
                        $status = $this->emailSubscriptionManager->getEmailSubscriptionStatusByQuoteId($cartId);
                        if ($status) {
                            $this->subscriberFactory->create()->subscribe($order->getCustomerEmail());
                        }
                    } catch (\Exception $e) {
                        $this->logger->alert($e->getMessage());
                    }
                }

                $createAccountEnabled = $this->checkoutConfig->isCreateAccountEnabled($order->getStoreId());
                if ($createAccountEnabled && $order->getId() && !$customer->getId()) {
                    /** New Customer */
                    $customer       = $this->orderCustomerService->create($orderId);
                    $customerSecure = $this->customerRegistry->retrieveSecureData($customer->getId());
                    $customerSecure->setRpToken(null);
                    $customerSecure->setRpTokenCreatedAt(null);

                    /** @var $quoteIdMaskEntity QuoteIdMask */
                    $quoteIdMaskEntity = $this->quoteIdMaskFactory->create()->load($cartId, 'quote_id');
                    $quoteIdMask       = $quoteIdMaskEntity->getMaskedId();
                    if (!$quoteIdMask) {
                        return;
                    }

                    try {
                        $passwordHash = $this->guestCustomerManagement->getTempPasswordHash($quoteIdMask);
                    } catch (NoSuchEntityException $noSuchEntityException) {
                        $this->logger->info($noSuchEntityException);

                        return;
                    }

                    $customerSecure->setPasswordHash($passwordHash);
                    $this->customerRepository->save($customer, $passwordHash);
                    $this->customerRegistry->remove($customer->getId());
                    $order->setCustomerId($customer->getId());
                    $order->setCustomerIsGuest(0);
                    $order->setCustomerFirstname($customer->getFirstname());
                    $order->setCustomerLastname($customer->getLastname());
                    $order->setCustomerGroupId($customer->getGroupId());
                    $this->saveAddress($order);
                    $this->orderRepository->save($order);
                    $this->messageManager->addSuccessMessage(__('You are successfully registered'));

                    $newCustomer = $this->customerRegistry->retrieveByEmail($customer->getEmail());
                    $this->customerSession->setCustomerAsLoggedIn($newCustomer);
                    $this->customerSession->setCustomerDataAsLoggedIn($customer);
                }
            }
        } catch (LocalizedException $localizedException) {
            $this->logger->alert($localizedException->getLogMessage());
        }
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface|\Magento\Sales\Model\Order $order
     */
    private function saveAddress(\Magento\Sales\Api\Data\OrderInterface $order): void
    {
        /** @var \Magento\Customer\Model\Address $address */
        $address      = $this->addressFactory->create();
        $orderAddress = $order->getShippingAddress() ? $order->getShippingAddress() : $order->getBillingAddress();
        $address->setData($orderAddress->getData());
        $address->setSaveInAddressBook(1);
        $address = $address->getDataModel();
        $address->setCustomerId($order->getCustomerId())
                ->setIsDefaultShipping('1');

        try {
            $this->addressRepository->save($address);
        } catch (LocalizedException $e) {
            $this->logger->alert($e->getLogMessage());
        }
    }
}
