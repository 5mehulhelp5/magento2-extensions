<?php
declare(strict_types=1);

namespace MageWorx\Checkout\Model;

class BillingAddressManagement implements \MageWorx\Checkout\Api\BillingAddressManagementInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $session;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var \Magento\Checkout\Model\PaymentDetailsFactory
     */
    private $paymentDetailsFactory;

    /**
     * @var \Magento\Quote\Api\CartTotalRepositoryInterface
     */
    private $cartTotalsRepository;

    /**
     * @var \Magento\Quote\Api\PaymentMethodManagementInterface
     */
    private $paymentMethodManagement;

    /**
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    private $userContext;

    /**
     * @param \Magento\Checkout\Model\Session $session
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory
     * @param \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
     * @param \Magento\Authorization\Model\UserContextInterface $userContext
     */
    public function __construct(
        \Magento\Checkout\Model\Session $session,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Authorization\Model\UserContextInterface $userContext
    ) {
        $this->session                 = $session;
        $this->cartRepository          = $cartRepository;
        $this->paymentDetailsFactory   = $paymentDetailsFactory;
        $this->cartTotalsRepository    = $cartTotalsRepository;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->userContext             = $userContext;
    }

    /**
     * @inheritDoc
     */
    public function saveBillingAddress(
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ): \Magento\Checkout\Api\Data\PaymentDetailsInterface {
        $cartId = (int)$this->session->getQuoteId();
        $quote  = $this->cartRepository->getActive($cartId);

        if ($this->userContext->getUserType() === \Magento\Authorization\Model\UserContextInterface::USER_TYPE_CUSTOMER
            && !$quote->getCustomerId()
        ) {
            $quote->setCustomerId($this->userContext->getUserId());
        }

        /**
         * Fix for new address of logged in customer: if it has no customer address id it must be overwritten with NULL
         * value, because magento use addData in the setBillingAddress method.
         */
        $billingAddress->setCustomerAddressId($billingAddress->getCustomerAddressId());

        $quote->setBillingAddress($billingAddress);
        $this->cartRepository->save($quote);

        $paymentDetails = $this->getPaymentInformation($cartId);

        return $paymentDetails;
    }

    /**
     * @inheritdoc
     */
    private function getPaymentInformation(int $cartId)
    {
        /** @var \Magento\Checkout\Api\Data\PaymentDetailsInterface $paymentDetails */
        $paymentDetails = $this->paymentDetailsFactory->create();
        $paymentDetails->setPaymentMethods($this->paymentMethodManagement->getList($cartId));
        $paymentDetails->setTotals($this->cartTotalsRepository->get($cartId));

        return $paymentDetails;
    }
}
