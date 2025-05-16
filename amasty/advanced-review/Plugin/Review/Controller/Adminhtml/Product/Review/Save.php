<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Plugin\Review\Controller\Adminhtml\Product\Review;

use Magento\Review\Controller\Adminhtml\Product\Save as MagentoReview;

class Save
{
    /**
     * @var \Amasty\AdvancedReview\Model\Repository\ImagesRepository
     */
    private $imagesRepository;

    public function __construct(
        \Amasty\AdvancedReview\Model\Repository\ImagesRepository $imagesRepository
    ) {
        $this->imagesRepository = $imagesRepository;
    }

    /**
     * @param MagentoReview $subject
     * @param $result
     * @return mixed
     */
    public function afterExecute(
        MagentoReview $subject,
        $result
    ) {
        $this->removeImages($subject);

        return $result;
    }

    /**
     * @param MagentoReview $subject
     */
    private function removeImages(MagentoReview $subject)
    {
        $images = $subject->getRequest()->getParam('review_remove_image', []);
        if (is_array($images)) {
            foreach ($images as $id => $image) {
                $this->imagesRepository->deleteById($id);
            }
        }
    }
}
