<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Observer\Review;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Delete implements ObserverInterface
{
    /**
     * @var \Amasty\AdvancedReview\Api\ImagesRepositoryInterface
     */
    private $imagesRepository;

    public function __construct(\Amasty\AdvancedReview\Api\ImagesRepositoryInterface $imagesRepository)
    {
        $this->imagesRepository = $imagesRepository;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $review = $observer->getObject();
        if ($review) {
            $this->imagesRepository->deleteByReviewId($review->getId());
        }
    }
}
