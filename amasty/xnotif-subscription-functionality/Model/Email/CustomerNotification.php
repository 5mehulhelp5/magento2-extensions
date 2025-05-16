<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Model\Email;

use Amasty\Xnotif\Model\Notification\DefaultAlert\CustomerRegistry;
use Amasty\XnotifSubscriptionFunctionality\Model\ConfigProvider;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\App\Area;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class CustomerNotification
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TypeProcessorProvider
     */
    private $processorProvider;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var CustomerInterfaceFactory
     */
    private $customerFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(
        ConfigProvider $configProvider,
        TransportBuilder $transportBuilder,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        TypeProcessorProvider $processorProvider,
        CustomerRepositoryInterface $customerRepository,
        CustomerRegistry $customerRegistry,
        CustomerInterfaceFactory $customerFactory,
        ProductRepositoryInterface $productRepository
    ) {
        $this->configProvider = $configProvider;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->processorProvider = $processorProvider;
        $this->customerRepository = $customerRepository;
        $this->customerRegistry = $customerRegistry;
        $this->customerFactory = $customerFactory;
        $this->productRepository = $productRepository;
    }

    public function sendEmail(AbstractModel $model, string $notificationType): void
    {
        if (!$this->isNotificationAvailable($notificationType)) {
            return;
        }

        try {
            $notificationProcessor = $this->processorProvider->getProcessorByType($notificationType);
            $unsubscribeUrlProcessor = $this->processorProvider->getUnsubscribeUrlByType($notificationType);

            $storeId = (int)$model->getStoreId();
            $product = $this->productRepository->getById($model->getProductId(), false, $storeId);

            // Customer object is needed for generation unsubscribeUrl.
            $customer = $this->prepareCustomerObject($model);
            $unsubscribeUrl = $unsubscribeUrlProcessor->getProductUnsubscribeUrl($model->getProductId());

            $transport = $this->transportBuilder
                ->setTemplateIdentifier($notificationProcessor->getTemplate())
                ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
                ->setTemplateVars([
                    'product_name' => $product->getName() ?? '',
                    'store_name' => $this->storeManager->getStore($storeId)->getName(),
                    'unsubscribeUrl' => $unsubscribeUrl
                ])
                ->setFromByScope($this->configProvider->getEmailSender(), $storeId)
                ->addTo($customer->getEmail())
                ->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }

    private function isNotificationAvailable(string $notificationType): bool
    {
        $isNotify = true;
        $notificationProcessor = $this->processorProvider->getProcessorByType($notificationType);
        $unsubscribeUrlProcessor = $this->processorProvider->getUnsubscribeUrlByType($notificationType);
        if (null === $notificationProcessor || null === $unsubscribeUrlProcessor) {
            $isNotify = false;
        }

        if (!$notificationProcessor->isNotificationEnabled()) {
            $isNotify = false;
        }

        return $isNotify;
    }

    private function prepareCustomerObject(AbstractModel $model): CustomerInterface
    {
        if ($model->getCustomerId()) {
            $customer = $this->customerRepository->getById((int)$model->getCustomerId());
        } else {
            $customer = $this->customerFactory->create();
            $customer->setEmail($model->getEmail());
        }

        $this->customerRegistry->clear();
        $this->customerRegistry->register($customer);

        return $customer;
    }
}
