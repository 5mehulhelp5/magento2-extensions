<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Controller\Adminhtml\Review;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Review\Model\ResourceModel\Review;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\StoreManagerInterface;

class MassAllApproveWebsite extends Action
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
     * @var Review
     */
    private $review;

    public function __construct(
        Action\Context $context,
        ReviewFactory $reviewFactory,
        StoreManagerInterface $storeManager,
        Review $review
    ) {
        parent::__construct($context);
        $this->reviewFactory = $reviewFactory;
        $this->storeManager = $storeManager;
        $this->review = $review;
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
                    $this->review->load($model, $reviewId);
                    $storeId = (int) $model->getStoreId();
                    $allStores = $this->getAllStoresIds($storeId);
                    $model->setStatusId(\Magento\Review\Model\Review::STATUS_APPROVED)
                        ->setData('stores', $allStores);
                    $this->review->save($model)->aggregate($model);
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
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    protected function getAllStoresIds(int $storeId): array
    {
        $websiteId = (int) $this->storeManager->getStore($storeId)->getWebsiteId();
        $stores = $this->storeManager->getWebsite($websiteId)->getStores();
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
