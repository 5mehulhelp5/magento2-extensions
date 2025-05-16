<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\ResourceModel\Review;

use Magento\Framework\App\ResourceConnection;

class RatingSummary
{
    public const COUNT_KEY = 'count';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param int[] $reviewIds
     * @param int $storeId
     * @return array{int: array{'count': int}}
     */
    public function getSummaryForReviews(array $reviewIds, int $storeId): array
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection
            ->select()
            ->from(
                ['rating_vote' => $this->resourceConnection->getTableName('rating_option_vote')],
                [
                    'percent',
                    self::COUNT_KEY => new \Zend_Db_Expr('COUNT(rating_vote.review_id)')
                ]
            )->joinInner(
                ['review_store' => $this->resourceConnection->getTableName('review_store')],
                'rating_vote.review_id = review_store.review_id',
                ['review_store.store_id']
            )
            ->where('rating_vote.review_id IN(?)', $reviewIds)
            ->where('review_store.store_id = ?', $storeId)
            ->group('rating_vote.percent');

        return $connection->fetchAssoc($select) ?: [];
    }
}
