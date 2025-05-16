<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\ResourceModel\Vote;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(
            \Amasty\AdvancedReview\Model\Vote::class,
            \Amasty\AdvancedReview\Model\ResourceModel\Vote::class
        );
    }

    /**
     * @return array
     */
    public function getVoteIpKeys()
    {
        $this->getSelect()->columns('CONCAT(review_id,ip) as vote_key');

        return $this->getColumnValues('vote_key');
    }
}
