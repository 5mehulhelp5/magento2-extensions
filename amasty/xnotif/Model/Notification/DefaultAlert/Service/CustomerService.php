<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Notification\DefaultAlert\Service;

use Amasty\Xnotif\Model\ConfigProvider;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\Data\Collection;
use Magento\Framework\Model\AbstractModel;
use Magento\ProductAlert\Model\Price as PriceAlert;
use Magento\ProductAlert\Model\ResourceModel\Price\Collection as PriceAlertCollection;
use Magento\ProductAlert\Model\ResourceModel\Stock\Collection as StockAlertCollection;
use Magento\ProductAlert\Model\Stock as StockAlert;
use Magento\Store\Model\StoreManagerInterface;

class CustomerService
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CustomerCollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var CustomerInterface[]|null
     */
    private $loadedCustomersByIds;

    /**
     * @var CustomerInterface[]|null
     */
    private $loadedCustomersByEmails;

    public function __construct(
        StoreManagerInterface $storeManager,
        CustomerCollectionFactory $customerCollectionFactory,
        CustomerFactory $customerFactory,
        ConfigProvider $configProvider
    ) {
        $this->storeManager = $storeManager;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->customerFactory = $customerFactory;
        $this->configProvider = $configProvider;
    }

    /**
     * Supported when all items in giving collection have same website_id.
     *
     * @param PriceAlertCollection|StockAlertCollection|Collection $alertCollection
     */
    public function preload(Collection $alertCollection): void
    {
        $this->clear();

        $retrieveCustomerDataModel = static function (CustomerModel $customerModel) {
            return $customerModel->getDataModel();
        };

        $this->loadedCustomersByIds = array_map(
            $retrieveCustomerDataModel,
            $this->loadCustomersByIds($alertCollection)
        );
        $this->loadedCustomersByEmails = array_map(
            $retrieveCustomerDataModel,
            $this->loadCustomersByEmails($alertCollection)
        );
    }

    /**
     * @param StockAlert|PriceAlert $alert
     */
    public function getCustomerForAlert(AbstractModel $alert): ?CustomerInterface
    {
        if ($alert->getCustomerId()) {
            $customer = $this->getById((int)$alert->getCustomerId());
        } else {
            $customer = $this->getByEmail((string)$alert->getEmail());
            if (!$customer) {
                $customer = $this->createCustomerModel($alert->getEmail(), (int)$alert->getWebsiteId());
            }
        }

        if ($customer) {
            $customer->setStoreId($alert->getStoreId());
        }

        return $customer;
    }

    public function getById(int $customerId): ?CustomerInterface
    {
        return $this->loadedCustomersByIds[$customerId] ?? null;
    }

    public function getByEmail(string $email): ?CustomerInterface
    {
        return $this->loadedCustomersByEmails[$email] ?? null;
    }

    public function clear(): void
    {
        $this->loadedCustomersByIds = null;
        $this->loadedCustomersByEmails = null;
    }

    /**
     * @param PriceAlertCollection|StockAlertCollection|Collection $alertCollection
     * @return CustomerModel[]
     */
    public function loadCustomersByIds(Collection $alertCollection): array
    {
        $customerIds = array_map(
            /** @var StockAlert|PriceAlert $alert */
            static function (AbstractModel $alert) {
                return (int)$alert->getCustomerId();
            },
            $alertCollection->getItems()
        );
        $customerIds = array_unique(array_filter($customerIds));

        return $this->loadCustomers([['entity_id', ['in' => $customerIds]]]);
    }

    /**
     * @param PriceAlertCollection|StockAlertCollection|Collection $alertCollection
     * @return CustomerModel[]
     */
    public function loadCustomersByEmails(Collection $alertCollection): array
    {
        /** @var StockAlert|PriceAlert $alert */
        $alert = $alertCollection->getFirstItem();
        if (!$alert->getId()) {
            return [];
        }

        $websiteId = (int)$alert->getWebsiteId();
        $customerEmails = array_map(
        /** @var StockAlert|PriceAlert $alert */
            static function (AbstractModel $alert) {
                return $alert->getCustomerId() ? '' : $alert->getEmail();
            },
            $alertCollection->getItems()
        );
        $customerEmails = array_unique(array_filter($customerEmails));

        $customers = $this->loadCustomers([
            ['email', ['in' => $customerEmails]],
            ['website_id', ['eq' => $websiteId]]
        ]);

        return array_combine(array_map(function ($customer) {
            return $customer->getEmail();
        }, $customers), $customers);
    }

    /**
     * @param array[] $conditions [['fieldName', ['operator' => 'value']], ...] Applies conditions with AND operator.
     * @return CustomerModel[]
     */
    private function loadCustomers(array $conditions): array
    {
        $customerCollection = $this->customerCollectionFactory->create();
        foreach ($conditions as $condition) {
            $customerCollection->addFieldToFilter(...$condition);
        }
        return $customerCollection->getItems();
    }

    private function createCustomerModel(string $email, int $websiteId): CustomerInterface
    {
        $customer = $this->customerFactory->create()->getDataModel();
        $customer->setWebsiteId($websiteId);
        $customer->setEmail($email);
        $customer->setLastname($this->configProvider->getGreetingTextForEmail());
        $customer->setGroupId(0);
        $customer->setId(0);

        return $customer;
    }
}
