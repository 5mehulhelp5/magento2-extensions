<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Product\Subscribe;

use Amasty\Xnotif\Model\Email\EmailValidator;
use Amasty\Xnotif\Model\Messages\ResultStatus;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\ProductAlert\Model\Price;
use Magento\ProductAlert\Model\ResourceModel\Price\Collection as PriceCollection;
use Magento\ProductAlert\Model\ResourceModel\Stock\Collection as StockCollection;
use Magento\ProductAlert\Model\Stock;
use Magento\Store\Model\StoreManagerInterface;

class PrepareModelForSave
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var EmailValidator
     */
    private $emailValidator;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        Session $session,
        EmailValidator $emailValidator,
        StoreManagerInterface $storeManager
    ) {
        $this->customerRepository = $customerRepository;
        $this->session = $session;
        $this->emailValidator = $emailValidator;
        $this->storeManager = $storeManager;
    }

    /**
     * @param int $productId
     * @param string|null $guestEmail
     * @param int|null $parentId
     * @param Price|Stock $model
     * @param StockCollection|PriceCollection $collection
     * @return array
     */
    public function execute(
        int $productId,
        AbstractModel $model,
        AbstractCollection $collection,
        ?string $guestEmail = null,
        ?int $parentId = null
    ): array {
        $resultModel = null;
        $websiteId = (int)$this->storeManager->getStore()->getWebsiteId();
        $model->setProductId($productId)
            ->setWebsiteId($websiteId)
            ->setStoreId($this->storeManager->getStore()->getId())
            ->setParentId($parentId);

        if ($guestEmail) {
            $guestEmail = $this->emailValidator->execute($guestEmail);

            try {
                $customer = $this->customerRepository->get($guestEmail, $websiteId);
                $model->setCustomerId($customer->getId());
                $collection->addFieldToFilter('customer_id', $customer->getId());
            } catch (NoSuchEntityException $exception) {
                $model->setEmail($guestEmail);
                $collection->addFieldToFilter('email', $guestEmail);
            }
        } else {
            $customerId = $this->session->getId();

            if ($customerId === null) {
                throw new LocalizedException(__('No logged in customers and email does not provided'));
            }

            $model->setCustomerId($customerId);
            $collection->addFieldToFilter('customer_id', $customerId);
        }

        if ($collection->getSize() > 0) {
            $status = ResultStatus::SUBSCRIPTION_ALREADY_EXIST_STATUS;
        } else {
            $resultModel = $model;
            $status = ResultStatus::SUBSCRIPTION_ADDED_STATUS;
        }

        return [$resultModel, $status];
    }
}
