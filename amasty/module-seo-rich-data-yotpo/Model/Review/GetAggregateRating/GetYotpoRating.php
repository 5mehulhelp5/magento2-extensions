<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Seo Rich Data Yotpo (System)
*/

declare(strict_types=1);

namespace Amasty\SeoRichDataYotpo\Model\Review\GetAggregateRating;

use Amasty\SeoRichData\Model\Review\GetAggregateRating\GenerateRatingRichData;
use Amasty\SeoRichData\Model\Review\GetAggregateRating\RatingProviderInterface;
use Forix\YotpoSyncProducts\Model\ResourceModel\ProductReview\Collection as YotpoReviewCollection;
use Forix\YotpoSyncProducts\Model\ResourceModel\ProductReview\CollectionFactory as YotpoReviewCollectionFactory;
use Forix\YotpoSyncProducts\Model\ProductReview;
use Magento\Catalog\Model\Product;

class GetYotpoRating implements RatingProviderInterface
{
    public const BEST_YOTPO_RATING = 5;

    /**
     * @var YotpoReviewCollectionFactory
     */
    private $yotpoReviewCollectionFactory;

    /**
     * @var GenerateRatingRichData
     */
    private $generateRatingRichData;

    public function __construct(
        YotpoReviewCollectionFactory $yotpoReviewCollectionFactory,
        GenerateRatingRichData $generateRatingRichData
    ) {
        $this->yotpoReviewCollectionFactory = $yotpoReviewCollectionFactory;
        $this->generateRatingRichData = $generateRatingRichData;
    }

    /**
     * @param Product $product
     * @param int $formatRating
     * @return array
     */
    public function execute(Product $product, int $formatRating): array
    {
        $ratingObject = $this->getRatingObject((int) $product->getId(), (int) $product->getStoreId());
        if ($ratingObject && $ratingObject->getRatingSummary() && $ratingObject->getTotalReviews()) {
            $rating = $this->generateRatingRichData->execute(
                (int) $ratingObject->getTotalReviews(),
                (float) $ratingObject->getRatingSummary(),
                self::BEST_YOTPO_RATING,
                $formatRating
            );
        } else {
            $rating = [];
        }

        return $rating;
    }

    private function getRatingObject(int $productId, int $storeId): ?ProductReview
    {
        /** @var YotpoReviewCollection $yotpoReviewCollection */
        $yotpoReviewCollection = $this->yotpoReviewCollectionFactory->create();
        $yotpoReviewCollection->addIdFilter([$productId]);
        $yotpoReviewCollection->addStoreFilter([$storeId]);
        $yotpoReviewCollection->setPageSize(1);

        return $yotpoReviewCollection->getFirstItem();
    }
}
