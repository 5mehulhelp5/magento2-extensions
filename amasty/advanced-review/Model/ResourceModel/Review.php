<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\ResourceModel;

class Review extends \Magento\Review\Model\ResourceModel\Review
{
    /**
     * @param int $reviewId
     * @param array $data
     */
    public function insertAdditionalData($reviewId, $data)
    {
        $connection = $this->getConnection();
        $reviewDetailTable = $this->getTable('review_detail');

        $select = $connection->select()
            ->from($reviewDetailTable, 'detail_id')
            ->where('review_id = ?', (int)$reviewId);
        $detailId = $connection->fetchOne($select);

        if ($detailId) {
            $condition = ["detail_id = ?" => $detailId];
            $connection->update($this->_reviewDetailTable, $data, $condition);
        }
    }
}
