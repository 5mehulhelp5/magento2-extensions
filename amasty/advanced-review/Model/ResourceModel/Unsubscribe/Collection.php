<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\ResourceModel\Unsubscribe;

use Amasty\AdvancedReview\Model\Unsubscribe;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(
            Unsubscribe::class,
            \Amasty\AdvancedReview\Model\ResourceModel\Unsubscribe::class
        );
    }
}
