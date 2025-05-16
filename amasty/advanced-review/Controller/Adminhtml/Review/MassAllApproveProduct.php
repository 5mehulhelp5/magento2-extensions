<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Controller\Adminhtml\Review;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Review\Model\ResourceModel\Review;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\StoreManagerInterface;

class MassAllApproveProduct extends Action
{
    /**
     * @var ReviewFactory
     */
    private $reviewFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * @var Review
     */
    private $reviewResource;

    public function __construct(
        Context $context,
        ReviewFactory $reviewFactory,
        StoreManagerInterface $storeManager,
        ProductResource $productResource,
        Review $reviewResource
    ) {
        parent::__construct($context);
        $this->reviewFactory = $reviewFactory;
        $this->storeManager = $storeManager;
        $this->productResource = $productResource;
        $this->reviewResource = $reviewResource;
    }

    /**
     * @return Redirect
     */
    public function execute()
    {
        $reviewsIds = $this->getRequest()->getParam('reviews');

        if (!is_array($reviewsIds)) {
            $this->messageManager->addErrorMessage(__('Please select review(s).'));
        } else {
            try {
                foreach ($reviewsIds as $reviewId) {
                    $model = $this->reviewFactory->create();
                    $this->reviewResource->load($model, $reviewId);
                    $websiteIdsByProductIds = $this->productResource->getWebsiteIdsByProductIds(
                        [$model->getEntityId()]
                    );
                    $allStores = $this->getAllStoresIds($websiteIdsByProductIds[$model->getEntityId()]);
                    $model->setStatusId(\Magento\Review\Model\Review::STATUS_APPROVED)
                        ->setData('stores', $allStores);
                    $this->reviewResource->save($model)->aggregate($model);
                }

                $this->messageManager->addSuccessMessage(
                    __('A total of %1 record(s) have been approved for All store views.', count($reviewsIds))
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while updating these review(s).')
                );
            }
        }
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('review/product/' . $this->getRequest()->getParam('ret', 'index'));
        return $resultRedirect;
    }

    /**
     * @throws LocalizedException
     */
    protected function getAllStoresIds(array $websiteIds): array
    {
        $stores = [];
        foreach ($websiteIds as $websiteId) {
            $stores += $this->storeManager->getWebsite($websiteId)->getStores();
        }

        $ids = [];

        foreach ($stores as $store) {
            $ids[] = $store->getId();
        }

        return $ids;
    }

    protected function _isAllowed(): bool
    {
        switch ($this->getRequest()->getParam('ret')) {
            case 'pending':
                $result = $this->_authorization->isAllowed('Magento_Review::pending');
                break;
            default:
                $result = $this->_authorization->isAllowed('Magento_Review::reviews_all');
                break;
        }

        return $result;
    }
}
