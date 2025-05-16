<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\ResourceModel\Reminder;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(
            \Amasty\AdvancedReview\Model\Reminder::class,
            \Amasty\AdvancedReview\Model\ResourceModel\Reminder::class
        );
        $this->_idFieldName = 'entity_id';
    }
}
