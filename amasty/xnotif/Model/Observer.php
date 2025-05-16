<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model;

use Amasty\Xnotif\Model\Email\ErrorEmailSender;
use Amasty\Xnotif\Model\Notification\DefaultAlert\CustomerRegistry;
use Amasty\Xnotif\Model\Notification\DefaultAlertProcessor;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\App\Emulation;

class Observer
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var AdminNotification
     */
    private $adminNotification;

    /**
     * @var Emulation
     */
    private $appEmulation;

    /**
     * @var \Amasty\Xnotif\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\ProductAlert\Model\EmailFactory
     */
    private $emailFactory;

    /**
     * @var ErrorEmailSender
     */
    private $errorEmailSender;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var DefaultAlertProcessor
     */
    private $defaultAlertProcessor;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\ProductAlert\Model\EmailFactory $emailFactory,
        \Magento\Framework\Registry $registry,
        \Amasty\Xnotif\Helper\Config $config,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Psr\Log\LoggerInterface $logger,
        AdminNotification $adminNotification,
        Emulation $appEmulation,
        ErrorEmailSender $errorEmailSender,
        CustomerRegistry $customerRegistry,
        DefaultAlertProcessor $defaultAlertProcessor,
        ProductRepositoryInterface $productRepository
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->emailFactory = $emailFactory;
        $this->customerFactory = $customerFactory;
        $this->registry = $registry;
        $this->logger = $logger;
        $this->adminNotification = $adminNotification;
        $this->appEmulation = $appEmulation;
        $this->config = $config;
        $this->errorEmailSender = $errorEmailSender;
        $this->customerRegistry = $customerRegistry;
        $this->defaultAlertProcessor = $defaultAlertProcessor;
        $this->productRepository = $productRepository;
    }

    /**
     * @return void
     */
    public function process(): void
    {
        $errors = $this->defaultAlertProcessor->execute('price');
        if (!$this->config->isQtyLimitEnabled()) {
            $errors = array_merge($errors, $this->defaultAlertProcessor->execute('stock'));
        }
        $this->sendErrorEmail($errors);
    }

    /**
     * @return void
     */
    public function runDailyCronJob(): void
    {
        $this->sendStockEmailsWithLimit();
        foreach ($this->storeManager->getStores() as $store) {
            $this->adminNotification->sendAdminNotifications((int)$store->getId());
        }
    }

    /**
     * @param int $productId
     * @param int $storeId
     *
     * @return bool|ProductInterface
     */
    protected function loadProduct($productId, $storeId)
    {
        try {
            $product = $this->productRepository->getById(
                $productId,
                false,
                $storeId
            );
        } catch (\Magento\Framework\Exception\NoSuchEntityException $ex) {
            $product = false;
        }

        if (!$product || $product->getStatus() == Status::STATUS_DISABLED) {
            $product = false;
        }

        return $product;
    }

    /**
     * @param \Magento\ProductAlert\Model\Stock $alert
     * @param $websiteId
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCustomerFromAlert($alert, $websiteId = null)
    {
        if (!$websiteId) {
            $websiteId = $this->storeManager->getStore()->getWebsite()->getId();
        }

        if ($alert->getCustomerId()) {
            try {
                $customer = $this->customerRepository->getById(
                    $alert->getCustomerId()
                );
            } catch (NoSuchEntityException $noSuchEntityException) {
                return null;
            }
        } else {
            try {
                $customer = $this->customerRepository->get(
                    $alert->getEmail(),
                    $websiteId
                );
            } catch (NoSuchEntityException $e) {
                $customer = $this->createCustomerModel($alert->getEmail(), $websiteId);
            }
        }

        $customer->setStoreId($alert->getStoreId());

        return $customer;
    }

    /**
     * @param string $email
     * @param int $websiteId
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    protected function createCustomerModel($email, $websiteId)
    {
        $customer = $this->customerFactory->create()->getDataModel();
        $customer->setWebsiteId(
            $websiteId
        )->setEmail(
            $email
        )->setLastname(
            $this->config->getCustomerName()
        )->setGroupId(
            0
        )->setId(
            0
        );

        return $customer;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    protected function getTestEmail()
    {
        $emailAddress = $this->config->getTestEmail();
        if (!$emailAddress) {
            throw new LocalizedException(
                __(
                    'Please specify email address: Store -> Configuration -> '
                    . 'Amasty Out of Stock Notification -> Test Stock Notification'
                )
            );
        }

        return $emailAddress;
    }

    /**
     * @param $alert
     * @throws LocalizedException
     */
    public function sendTestNotification($alert)
    {
        $alert->setCustomerId(null)->setEmail($this->getTestEmail());

        /** @var \Magento\ProductAlert\Model\Email  $email */
        $email = $this->emailFactory->create();
        $email->setType('stock');

        $websiteId = $alert->getWebsiteId();
        $websiteId = explode(',', $websiteId);
        $websiteId = $websiteId[0];

        $website = $this->storeManager->getWebsite($websiteId);
        $storeId = $alert->getStoreId() ? $alert->getStoreId() : $website->getDefaultStore()->getId();
        $email->setWebsite($website)->setStoreId($storeId);
        if (!$this->scopeConfig->getValue(
            'catalog/productalert/allow_stock',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $website->getDefaultGroup()->getDefaultStore()->getId()
        )) {
            throw new LocalizedException(
                __('Please enable stock notifications: Store -> Configuration -> Catalog -> Alert')
            );
        }

        $productId = $alert->getProductId();
        if ($alert->getParentId()
            && $alert->getParentId() != $productId
        ) {
            $productId = $alert->getParentId();
        }

        $this->appEmulation->startEnvironmentEmulation($storeId, 'frontend', true);
        $product = $this->loadProduct($productId, $storeId);
        $customer = $this->getCustomerFromAlert($alert, $websiteId);
        $product->setCustomerGroupId($customer->getGroupId());

        $email->addStockProduct($product);
        $email->setCustomerData($customer);
        $this->saveTemporaryCustomer($customer);

        $this->registry->register('xnotif_test_notification', true);
        $email->send();
        $this->registry->unregister('xnotif_test_notification');
        $this->appEmulation->stopEnvironmentEmulation();
    }

    /**
     * Save customer for current iteration.
     */
    private function saveTemporaryCustomer(CustomerInterface $customer): void
    {
        $this->deleteTemporaryCustomer();
        $this->customerRegistry->register($customer);
    }

    private function deleteTemporaryCustomer()
    {
        $this->customerRegistry->clear();
    }

    /**
     * @return void
     */
    protected function sendStockEmailsWithLimit()
    {
        $errors = $this->defaultAlertProcessor->execute('stock');
        $this->sendErrorEmail($errors);
    }

    protected function sendErrorEmail(array $errors): void
    {
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->logger->error($error);
            }
        }

        $this->errorEmailSender->execute($errors);
    }
}
