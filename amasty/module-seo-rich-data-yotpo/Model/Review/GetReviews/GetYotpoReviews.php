<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Seo Rich Data Yotpo (System)
*/

declare(strict_types=1);

namespace Amasty\SeoRichDataYotpo\Model\Review\GetReviews;

use Amasty\SeoRichData\Model\Review\FormatRating;
use Amasty\SeoRichData\Model\Review\GetBestRating;
use Amasty\SeoRichData\Model\Review\GetReviews\GenerateReviewRichData;
use Amasty\SeoRichData\Model\Review\GetReviews\ReviewProviderInterface;
use Amasty\SeoRichData\Model\Review\ReviewBuilder;
use Amasty\SeoRichDataYotpo\Model\Review\GetAggregateRating\GetYotpoRating;
use Amasty\Yotpo\Model\Yotpo\Client;

class GetYotpoReviews implements ReviewProviderInterface
{
    public const REVIEWS_PER_REQUEST = 30;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var GenerateReviewRichData
     */
    private $generateReviewRichData;

    /**
     * @var ReviewBuilder
     */
    private $reviewBuilder;

    /**
     * @var FormatRating
     */
    private $formatRating;

    /**
     * @var GetBestRating
     */
    private $getBestRating;

    protected $productReviewResource;

    private \Forix\YotpoSyncProducts\Model\Api\Products $productsApi;

    public function __construct(
        Client $client,
        GenerateReviewRichData $generateReviewRichData,
        ReviewBuilder $reviewBuilder,
        FormatRating $formatRating,
        GetBestRating $getBestRating,
        \Forix\YotpoSyncProducts\Model\ResourceModel\ProductReview $productReviewResource,
        \Forix\YotpoSyncProducts\Model\Api\Products $productsApi
    ) {
        $this->client = $client;
        $this->generateReviewRichData = $generateReviewRichData;
        $this->reviewBuilder = $reviewBuilder;
        $this->formatRating = $formatRating;
        $this->getBestRating = $getBestRating;
        $this->productReviewResource = $productReviewResource;
        $this->productsApi = $productsApi;
    }

    /**
     * @param int $productId
     * @param int $storeIdFilter
     * @param int $numberReviews
     * @param int $formatRating
     * @return array
     */
    public function executeCustom(int $productId, int $storeIdFilter, int $numberReviews, int $formatRating): array
    {
        $reviewsRichData = [];
        foreach ($this->loadReviewsCustom($productId, $storeIdFilter, $numberReviews) as $review) {
            if (isset($review['score'])) {
                $bestRating = $this->getBestRating->execute($formatRating);
                $ratingValue = $this->formatRating->execute(
                    (float) $review['score'],
                    GetYotpoRating::BEST_YOTPO_RATING,
                    $bestRating
                );
            } else {
                $bestRating = 0;
                $ratingValue = 0;
            }

            $this->reviewBuilder->setNickname($review['user']['display_name'] ?? '');
            $this->reviewBuilder->setCreatedAt($review['created_at'] ?? '');
            $this->reviewBuilder->setTitle($review['title'] ?? '');
            $this->reviewBuilder->setDetail($review['content'] ?? '');
            $this->reviewBuilder->setRatingValue($ratingValue);
            $this->reviewBuilder->setBestRating($bestRating);

            $reviewsRichData[] = $this->generateReviewRichData->execute($this->reviewBuilder->create());
        }

        return $reviewsRichData;
    }

    /**
     * @param int $productId
     * @param int $storeIdFilter
     * @param int $numberReviews
     * @param int $formatRating
     * @return array
     */
    public function execute(int $productId, int $storeIdFilter, int $numberReviews, int $formatRating): array
    {
        $reviewsRichData = [];
        foreach ($this->loadReviews($productId, $storeIdFilter, $numberReviews) as $review) {
            if (isset($review['reviewRating']['ratingValue'])) {
                $ratingValue = $review['reviewRating']['ratingValue'];
            } else {
                $ratingValue = 0;
            }

            if (isset($review['reviewRating']['bestRating'])) {
                $bestRating = $review['reviewRating']['bestRating'];
            } else {
                $bestRating = 0;
            }

            if (isset($review['author']['name'])) {
                $this->reviewBuilder->setNickname($review['author']['name'] ?? '');
            }
            else{
                $this->reviewBuilder->setNickname('');
            }

            $this->reviewBuilder->setCreatedAt($review['datePublished'] ?? '');
            $this->reviewBuilder->setTitle(htmlentities($review['name']) ?? '');
            $this->reviewBuilder->setDetail(htmlentities($review['reviewBody']) ?? '');
            $this->reviewBuilder->setRatingValue($ratingValue);
            $this->reviewBuilder->setBestRating($bestRating);

            $reviewsRichData[] = $this->generateReviewRichData->execute($this->reviewBuilder->create());
        }

        return $reviewsRichData;
    }

    /**
     * @param int $productId
     * @param int $storeIdFilter
     * @param int $numberReviews
     * @return array
     */
    private function loadReviews(int $productId, int $storeIdFilter, int $numberReviews): array
    {
        return $this->productReviewResource->getProductReviewBy($productId,$storeIdFilter);
    }

    /**
     * @param int $productId
     * @param int $storeIdFilter
     * @param int $numberReviews
     * @return array
     */
    private function loadReviewsCustom(int $productId, int $storeIdFilter, int $numberReviews): array
    {
        $lastPage = ceil($numberReviews / static::REVIEWS_PER_REQUEST);
        $page = 1;

        $reviews = [];
        while ($page <= $lastPage) {
            if ($numberReviews <= static::REVIEWS_PER_REQUEST) {
                $numberReviewsToLoad = $numberReviews;
            } else {
                $numberReviewsToLoad = static::REVIEWS_PER_REQUEST;
                $numberReviews -= static::REVIEWS_PER_REQUEST;
            }
            $newReviews = $this->loadReviewsCustomByPage($productId, $storeIdFilter, $numberReviewsToLoad, $page);
            array_push($reviews, ...$newReviews);
            $page++;

            if (count($newReviews) < static::REVIEWS_PER_REQUEST) {
                break; // determine last page on yotpo server found
            }
        }

        return $reviews;
    }

    /**
     * @param int $productId
     * @param int $storeIdFilter
     * @param int $numberReviews
     * @param int $page
     * @return array
     */
    private function loadReviewsByPage(int $productId, int $storeIdFilter, int $numberReviews, int $page): array
    {
        return $this->client->getProductReviews($productId, $storeIdFilter, $page, $numberReviews);
    }

    /**
     * @param int $productId
     * @param int $storeIdFilter
     * @param int $numberReviews
     * @param int $page
     * @return array
     */
    private function loadReviewsCustomByPage(int $productId, int $storeIdFilter, int $numberReviews, int $page): array
    {
        return $this->productsApi->getProductReviews($productId, $storeIdFilter, $page, $numberReviews);
    }
}
