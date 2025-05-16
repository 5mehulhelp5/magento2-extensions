<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Api\Data;

interface QuestionInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    public const QUESTION_ID = 'question_id';
    public const TITLE = 'title';
    public const SHORT_ANSWER = 'short_answer';
    public const ANSWER = 'answer';
    public const VISIBILITY = 'visibility';
    public const STATUS = 'status';
    public const NAME = 'name';
    public const EMAIL = 'email';
    public const POSITION = 'position';
    public const URL_KEY = 'url_key';
    public const POSITIVE_RATING = 'positive_rating';
    public const NEGATIVE_RATING = 'negative_rating';
    public const TOTAL_RATING = 'total_rating';
    public const AVERAGE_RATING = 'avg_rating';
    public const AVERAGE_TOTAL = 'avg_total';
    public const META_TITLE = 'meta_title';
    public const META_DESCRIPTION = 'meta_description';
    public const META_ROBOTS = 'meta_robots';
    public const CREATED_AT = 'created_at';
    public const STORES = 'store_ids';
    public const CATEGORIES = 'category_ids';
    public const TAGS = 'tags';
    public const VISIT_COUNT = 'visit_count';
    public const EXCLUDE_SITEMAP = 'exclude_sitemap';
    public const UPDATED_AT = 'updated_at';
    public const CANONICAL_URL = 'canonical_url';
    public const NOINDEX = 'noindex';
    public const NOFOLLOW = 'nofollow';
    public const IS_SHOW_FULL_ANSWER = 'is_show_full_answer';
    public const PRODUCT_IDS = 'product_ids';
    public const ASKED_FROM_STORE = 'asked_from_store';
    public const CUSTOMER_GROUPS = 'customer_groups';
    /**#@-*/

    /**
     * @return int
     */
    public function getQuestionId();

    /**
     * @param int $questionId
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setQuestionId($questionId);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getAnswer();

    /**
     * @param string $answer
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setAnswer($answer);

    /**
     * @return string
     */
    public function getRelativeUrl();

    /**
     * @return string
     */
    public function getShortAnswer();

    /**
     * @param string $shortAnswer
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setShortAnswer($shortAnswer);

    /**
     * @return int
     */
    public function getVisibility();

    /**
     * @param int $visibility
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setVisibility($visibility);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setStatus($status);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string|null $name
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setEmail($email);

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param int $position
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setPosition($position);

    /**
     * @return string|null
     */
    public function getUrlKey();

    /**
     * @param string|null $urlKey
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setUrlKey($urlKey);

    /**
     * @return int|null
     */
    public function getPositiveRating();

    /**
     * @param int|null $rating
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setPositiveRating($rating);

    /**
     * @return int|null
     */
    public function getNegativeRating();

    /**
     * @param int|null $rating
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setNegativeRating($rating);

    /**
     * @return int|null
     */
    public function getTotalRating();

    /**
     * @param int|null $rating
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setTotalRating($rating);

    /**
     * @return float
     */
    public function getAverageRating();

    /**
     * @param $rating
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setAverageRating($rating);

    /**
     * @return int
     */
    public function getAverageTotal();

    /**
     * @param $total
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setAverageTotal($total);

    /**
     * @return string|null
     */
    public function getMetaTitle();

    /**
     * @param string|null $metaTitle
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setMetaTitle($metaTitle);

    /**
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * @param string|null $metaDescription
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setMetaDescription($metaDescription);

    /**
     * @return string|null
     */
    public function getMetaRobots();

    /**
     * @param string|null $metaRobots
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setMetaRobots($metaRobots);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string|null
     */
    public function getStores();

    /**
     * @param string $stores
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setStores($stores);

    /**
     * @return string|null
     */
    public function getCategories();

    /**
     * @param string $categories
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setCategories($categories);

    /**
     * @return \Amasty\Faq\Api\Data\TagInterface[]
     */
    public function getTags();

    /**
     * @param \Amasty\Faq\Api\Data\TagInterface[]
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setTags($tags);

    /**
     * @return int
     */
    public function getVisitCount();

    /**
     * @param int $count
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setVisitCount($count);

    /**
     * @return bool
     */
    public function getExcludeSitemap();

    /**
     * @param bool $exclude
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setExcludeSitemap($exclude);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param $canonicalUrl
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setCanonicalUrl($canonicalUrl);

    /**
     * @return string
     */
    public function getCanonicalUrl();

    /**
     * @return bool
     */
    public function isNoindex();

    /**
     * @return bool
     */
    public function isNofollow();

    /**
     * @param bool $isShow
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setIsShowFullAnswer($isShow);

    /**
     * @return bool
     */
    public function isShowFullAnswer();

    /**
     * @return string|null
     */
    public function getProductIds();

    /**
     * @param string $productIds
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setProductIds($productIds);

    /**
     * @return int|null
     */
    public function getAskedFromStore();

    /**
     * @param int $askedFromStore
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setAskedFromStore($askedFromStore);

    /**
     * @return string|null
     */
    public function getCustomerGroups();

    /**
     * @param string $customerGroups
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function setCustomerGroups($customerGroups);
}
