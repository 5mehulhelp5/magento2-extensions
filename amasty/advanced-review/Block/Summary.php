<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Block;

use Amasty\AdvancedReview\Model\ResourceModel\Review\RatingSummary;
use Amasty\AdvancedReview\Model\Toolbar\UrlBuilder as UrlBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;
use Magento\Framework\View\Element\Template;
use Amasty\AdvancedReview\Model\Sources\Recommend;
use Magento\Store\Model\Store;
use Magento\Catalog\Model\Product;

class Summary extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_AdvancedReview::summary.phtml';

    /**
     * @var null|int
     */
    private $votedRecommendCount = null;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $product;

    /**
     * Review collection
     *
     * @var ReviewCollection
     */
    private $reviewsCollection;

    /**
     * @var \Magento\Review\Model\Review\SummaryFactory
     */
    private $summaryModFactory;

    /**
     * @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory
     */
    private $reviewsColFactory;

    /**
     * @var \Amasty\AdvancedReview\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @var RatingSummary
     */
    private $ratingSummary;

    public function __construct(
        Template\Context $context,
        \Magento\Review\Model\Review\SummaryFactory $summaryModFactory,
        ?\Magento\Review\Model\RatingFactory $ratingFactory, // TODO remove
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory,
        \Amasty\AdvancedReview\Helper\Config $configHelper,
        ?\Amasty\AdvancedReview\Helper\BlockHelper $advancedHelper, // TODO remove
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        UrlBuilder $urlBuilder,
        array $data = [],
        RatingSummary $ratingSummary = null // TODO move to not optional
    ) {
        parent::__construct($context, $data);
        $this->summaryModFactory = $summaryModFactory;
        $this->reviewsColFactory = $collectionFactory;
        $this->configHelper = $configHelper;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->urlBuilder = $urlBuilder;
        // OM for backward compatibility
        $this->ratingSummary = $ratingSummary ?? ObjectManager::getInstance()->get(RatingSummary::class);
    }

    /**
     * Get collection of reviews
     *
     * @return ReviewCollection
     */
    public function getReviewsCollection()
    {
        if (null === $this->reviewsCollection) {
            $this->reviewsCollection = $this->reviewsColFactory->create()->addStoreFilter(
                $this->_storeManager->getStore()->getId()
            )->addStatusFilter(
                \Magento\Review\Model\Review::STATUS_APPROVED
            )->addEntityFilter(
                'product',
                $this->getProduct()->getId()
            )->setDateOrder();
        }
        return $this->reviewsCollection;
    }

    /**
     * @return bool
     */
    public function shouldShowRecommended()
    {
        $result = $this->configHelper->isRecommendFieldEnabled()
            && $this->getRecommendedVotedCount();

        return $result;
    }

    /**
     * Get ratings summary
     *
     * @return string
     */
    public function getRatingSummary()
    {
        $result = null;
        $summary = $this->getProduct()->getRatingSummary();
        if ($summary) {
            $result = $summary->getRatingSummary();
        }
        return $result;
    }

    /**
     * Get detail summary
     *
     * @return array
     */
    public function getDetailedSummary()
    {
        $result = [
            '5' => 0,
            '4' => 0,
            '3' => 0,
            '2' => 0,
            '1' => 0
        ];

        $reviewSummaryResult = $this->ratingSummary->getSummaryForReviews(
            $this->getReviewsCollection()->getAllIds(),
            (int)$this->_storeManager->getStore()->getId()
        );

        foreach ($reviewSummaryResult as $rating => $summaryData) {
            $count = $summaryData[RatingSummary::COUNT_KEY] ?: 1;
            $key = (string)floor($rating / 20);
            if (array_key_exists($key, $result)) {
                $result[$key] += $count;
            }
        }

        if ($filterStars = $this->getDisplayedCollection()->getFlag('filter_by_stars')) {
            $this->getDisplayedCollection()->setFlag('items_count', $result[$filterStars]);
        }

        return $result;
    }

    public function getDisplayedCollection(): ReviewCollection
    {
        return $this->getData('displayed_collection');
    }

    public function setDisplayedCollection(ReviewCollection $collection): Summary
    {
        $this->setData('displayed_collection', $collection);

        return $this;
    }

    /**
     * Get ratings summary
     *
     * @return string
     */
    public function getRatingSummaryValue()
    {
        $value = $this->getRatingSummary();
        $value = $value / 100 * 5;

        return round($value, 1);
    }

    /**
     * @return int
     */
    public function getRecomendedPercent()
    {
        $collection = $this->getRecommendedCollection()
            ->addFieldToFilter('is_recommended', Recommend::RECOMMENDED);

        $result = 0;
        $totalCount = $this->getRecommendedVotedCount();
        if ($totalCount) {
            $result = round($collection->getSize() / $totalCount * 100);
        }

        return $result;
    }

    /**
     * @return ReviewCollection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getRecommendedCollection()
    {
        $collection = $this->reviewsColFactory->create()->addStoreFilter(
            $this->_storeManager->getStore()->getId()
        )->addStatusFilter(
            \Magento\Review\Model\Review::STATUS_APPROVED
        )->addEntityFilter(
            'product',
            $this->getProduct()->getId()
        )->addFieldToFilter(
            'main_table.entity_pk_value',
            $this->getProduct()->getId()
        );

        return $collection;
    }

    /**
     * @return int|null
     */
    private function getRecommendedVotedCount()
    {
        if ($this->votedRecommendCount == null) {
            $collection = $this->getRecommendedCollection()
                ->addFieldToFilter(
                    'is_recommended',
                    ['in' => [Recommend::RECOMMENDED, Recommend::NOT_RECOMMENDED]]
                );

            $this->votedRecommendCount = $collection->getSize();
        }

        return $this->votedRecommendCount;
    }

    /**
     * Get count of reviews
     *
     * @return int
     */
    public function getReviewsCount()
    {
        return $this->getReviewsCollection()->getSize();
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Product $product
     *
     * @return $this
     */
    public function setProduct(\Magento\Catalog\Model\Product $product)
    {
        $this->product = $product;
        $this->applySummary($this->product, $this->_storeManager->getStore()->getId());
        return $this;
    }

    /**
     * @param $product
     * @param int $storeId
     */
    private function applySummary($product, $storeId = Store::DEFAULT_STORE_ID)
    {
        $summaryData = $this->summaryModFactory->create()
            ->setStoreId($storeId)
            ->load($product->getId());
        $summary = $this->dataObjectFactory->create();
        $summary->setData($summaryData->getData());
        $product->setRatingSummary($summary);
    }

    /**
     * Get URL for ajax filtering call
     *
     * @param $countStars
     *
     * @return string
     */
    public function getProductReviewUrl($countStars)
    {
        return $this->urlBuilder->generateUrl(UrlBuilder::STARS_PARAM_NAME, $countStars);
    }
}
