<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\ResourceModel;

use Amasty\Faq\Api\Data\VisitStatInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class VisitStat extends AbstractDb
{
    public const TABLE_NAME = 'amasty_faq_view_stat';

    public function _construct()
    {
        $this->_init(self::TABLE_NAME, VisitStatInterface::VISIT_ID);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function clearTable()
    {
        $this->getConnection()->truncateTable($this->getMainTable());
    }
}
