<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\ResourceModel;

class Reminder extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public const MAIN_TABLE = 'amasty_advanced_review_reminder';

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_setResource('sales');
        $this->_init(self::MAIN_TABLE, 'entity_id');
    }
}
