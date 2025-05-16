<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Notification;

use Amasty\Xnotif\Model\Analytics\Collector;
use Amasty\Xnotif\Model\Email\AlertEmail;
use Amasty\Xnotif\Model\Email\AlertEmailFactory;
use Amasty\Xnotif\Model\Notification\DefaultAlert\AlertIterator;
use Amasty\Xnotif\Model\Notification\DefaultAlert\CustomerRegistry;
use Amasty\Xnotif\Model\Notification\DefaultAlert\Processor\Price as PriceAlertProcessor;
use Amasty\Xnotif\Model\Notification\DefaultAlert\Processor\Stock as StockAlertProcessor;
use Amasty\Xnotif\Model\Notification\DefaultAlert\Service\CustomerService;
use Amasty\Xnotif\Model\Notification\DefaultAlert\Service\ProductService;
use Exception;
use Generator;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Area;
use Magento\Framework\Registry;
use Magento\ProductAlert\Model\Price as PriceAlert;
use Magento\ProductAlert\Model\Stock as StockAlert;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;

class DefaultAlertProcessor
{
    /**
     * @var AlertEmailFactory
     */
    private $alertEmailFactory;

    /**
     * @var AlertIterator
     */
    private $alertIterator;

    /**
     * @var Emulation
     */
    private $appEmulation;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var CustomerService
     */
    private $customerService;

    /**
     * @var ProductService
     */
    private $productService;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var StockAlertProcessor
     */
    private $stockAlertProcessor;

    /**
     * @var PriceAlertProcessor
     */
    private $priceAlertProcessor;

    /**
     * @var Collector
     */
    private $collector;

    /**
     * @var int
     */
    private $productNotificationsCounter = 0;

    /**
     * @var int
     */
    private $tempNotificationsCounter = 0;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var Email
     */
    private $email;

    public function __construct(
        AlertEmailFactory $alertEmailFactory,
        AlertIterator $alertIterator,
        Emulation $appEmulation,
        Registry $registry,
        CustomerService $customerService,
        ProductService $productService,
        StoreManagerInterface $storeManager,
        CustomerRegistry $customerRegistry,
        StockAlertProcessor $stockAlertProcessor,
        PriceAlertProcessor $priceAlertProcessor,
        Collector $collector
    ) {
        $this->alertEmailFactory = $alertEmailFactory;
        $this->alertIterator = $alertIterator;
        $this->appEmulation = $appEmulation;
        $this->registry = $registry;
        $this->customerService = $customerService;
        $this->productService = $productService;
        $this->storeManager = $storeManager;
        $this->customerRegistry = $customerRegistry;
        $this->stockAlertProcessor = $stockAlertProcessor;
        $this->priceAlertProcessor = $priceAlertProcessor;
        $this->collector = $collector;
    }

    /**
     * @return array Errors.
     */
    public function execute(string $alertType): array
    {
        $this->errors = [];

        $email = $this->getEmailModel();
        $email->clean();
        $email->setType($alertType);

        $previousCustomerEmail = null;

        /** @var Generator $alerts */
        foreach ($this->alertIterator->getItems($alertType) as $storeId => $alerts) {
            $this->send();

            if (!$alerts->current()) {
                continue;
            }

            $this->startEmulateStore($storeId);

            $email->setWebsiteId($this->storeManager->getStore($storeId)->getWebsiteId());
            $email->setStoreId($storeId);

            /** @var StockAlert|PriceAlert $alert $alert */
            foreach ($alerts as $alert) {
                $customer = $this->customerService->getCustomerForAlert($alert);
                if (!$customer) {
                    continue;
                }

                if ($previousCustomerEmail !== $customer->getEmail()) {
                    $this->send();
                }

                $previousCustomerEmail = $customer->getEmail();
                $email->setCustomerData($customer);
                $this->saveTemporaryCustomer($customer);

                $product = $this->productService->get((int)$alert->getProductId());
                if (!$product) {
                    continue;
                }

                try {
                    if ('stock' == $alertType) {
                        if ($product = $this->stockAlertProcessor->execute($product, $alert)) {
                            $product->setCustomerGroupId($customer->getGroupId());
                            $email->addStockProduct($product);
                            $this->tempNotificationsCounter++;
                        }
                    } else {
                        if ($product = $this->priceAlertProcessor->execute($product, $alert)) {
                            $email->addPriceProduct($product);
                        }
                    }
                } catch (Exception $e) {
                    $this->errors[] = $e->getMessage();
                    $this->tempNotificationsCounter = 0;
                }
            }
        }

        $this->send();
        $this->stopEmulateStore();

        if ($this->productNotificationsCounter) {
            $this->collector->updateDaily(Collector::ACTION_SENT, $this->productNotificationsCounter);
        }

        return $this->errors;
    }

    private function send(): void
    {
        $email = $this->getEmailModel();
        if (!$email->getStoreId()) {
            return;
        }

        try {
            $email->send();
            $email->clean();
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            $this->tempNotificationsCounter = 0;
        }

        $this->productNotificationsCounter += $this->tempNotificationsCounter;
        $this->tempNotificationsCounter = 0;
        $this->deleteTemporaryCustomer();
    }

    /**
     * Save customer for current iteration.
     */
    private function saveTemporaryCustomer(CustomerInterface $customer): void
    {
        $this->deleteTemporaryCustomer();
        $this->customerRegistry->register($customer);
    }

    private function deleteTemporaryCustomer(): void
    {
        $this->customerRegistry->clear();
    }

    private function getEmailModel(): AlertEmail
    {
        if ($this->email === null) {
            $this->email = $this->alertEmailFactory->create();
        }
        return $this->email;
    }

    private function startEmulateStore(int $storeId): void
    {
        $this->stopEmulateStore();
        $this->appEmulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);
        $this->registerAmastyStore($storeId);
    }

    private function stopEmulateStore()
    {
        $this->appEmulation->stopEnvironmentEmulation();
        $this->unregisterAmastyStore();
    }

    private function registerAmastyStore(int $storeId): void
    {
        $this->unregisterAmastyStore();
        $this->registry->register('amasty_store_id', $storeId);
    }

    private function unregisterAmastyStore()
    {
        $this->registry->unregister('amasty_store_id');
    }
}
