<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Api\Data;

interface VoteInterface
{
    public const VOTE_ID = 'vote_id';
    public const REVIEW_ID = 'review_id';
    public const TYPE = 'type';
    public const IP = 'ip';

    /**
     * Returns vote id field
     *
     * @return int|null
     */
    public function getVoteId();

    /**
     * @param int $voteId
     *
     * @return $this
     */
    public function setVoteId($voteId);

    /**
     * Returns review id field
     *
     * @return int|null
     */
    public function getReviewId();

    /**
     * @param int $reviewId
     *
     * @return $this
     */
    public function setReviewId($reviewId);

    /**
     * Returns vote path
     *
     * @return int|null
     */
    public function getType();

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type);

    /**
     * Returns vote path
     *
     * @return string|null
     */
    public function getIp();

    /**
     * @param string $ip
     *
     * @return $this
     */
    public function setIp($ip);
}
