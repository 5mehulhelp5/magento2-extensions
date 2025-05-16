<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\ResourceModel\Images;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(
            \Amasty\AdvancedReview\Model\Images::class,
            \Amasty\AdvancedReview\Model\ResourceModel\Images::class
        );
    }

    /**
     * @return array
     */
    public function getImageKeys()
    {
        $this->getSelect()->columns('CONCAT(review_id,path) as image_key');

        return $this->getColumnValues('image_key');
    }
}
