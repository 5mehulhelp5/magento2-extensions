<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Controller\Onepage;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Checkout\Model\Type\Onepage as CheckoutTypeOnepage;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory as ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page as ResultPage;
use MageWorx\Checkout\Api\CheckoutConfigInterface as CheckoutConfigHelper;
use Magento\Checkout\Helper\Data as RegularCheckoutHelper;

/**
 * Class Index
 *
 * Main controller for MageWorx Checkout
 */
class Index extends Action
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var CheckoutTypeOnepage
     */
    protected $onepageCheckout;

    /**
     * @var CheckoutConfigHelper
     */
    protected $checkoutConfigHelper;

    /**
     * @var ResultInterface|ResultPage
     */
    protected $resultPage;

    /**
     * @var RegularCheckoutHelper
     */
    protected $regularCheckoutHelper;

    /**
     * Refresh constructor.
     *
     * @param Context $context
     * @param Session $customerSession
     * @param JsonFactory $resultJsonFactory
     * @param CheckoutSession $checkoutSession
     * @param CheckoutTypeOnepage $onepageCheckout
     * @param CheckoutConfigHelper $checkoutConfigHelper
     * @param RegularCheckoutHelper $regularCheckoutHelper
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        JsonFactory $resultJsonFactory,
        CheckoutSession $checkoutSession,
        CheckoutTypeOnepage $onepageCheckout,
        CheckoutConfigHelper $checkoutConfigHelper,
        RegularCheckoutHelper $regularCheckoutHelper
    ) {
        $this->session               = $customerSession;
        $this->jsonResultFactory     = $resultJsonFactory;
        $this->checkoutSession       = $checkoutSession;
        $this->onepageCheckout       = $onepageCheckout;
        $this->checkoutConfigHelper  = $checkoutConfigHelper;
        $this->regularCheckoutHelper = $regularCheckoutHelper;

        parent::__construct($context);
    }

    /**
     * Checkout page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->checkoutConfigHelper->isEnabled()) {
            // Redirect to regular checkout when disabled in admin
            return $this->resultRedirectFactory->create()->setPath('checkout');
        }

        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        if (!$this->session->isLoggedIn() && !$this->regularCheckoutHelper->isAllowedGuestCheckout($quote)) {
            $this->messageManager->addErrorMessage(__('Guest checkout is disabled.'));

            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        if (!$this->isSecureRequest()) {
            $this->session->regenerateId();
        }
        $this->checkoutSession->setCartWasUpdated(false);

        $this->getOnepage()->initCheckout();
        /** @var ResultPage resultPage */
        $this->resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $this->resultPage->getConfig()->getTitle()->set($this->checkoutConfigHelper->getCheckoutPageTitle());

        return $this->resultPage;
    }

    /**
     * Get one page checkout model
     *
     * @return CheckoutTypeOnepage
     */
    public function getOnepage()
    {
        return $this->onepageCheckout;
    }

    /**
     * Checks if current request uses SSL and referer also is secure.
     *
     * @return bool
     */
    private function isSecureRequest(): bool
    {
        $request = $this->getRequest();

        $referrer = $request->getHeader('referer');
        $secure   = false;

        if ($referrer) {
            $scheme = parse_url($referrer, PHP_URL_SCHEME);
            $secure = $scheme === 'https';
        }

        return $secure && $request->isSecure();
    }
}
