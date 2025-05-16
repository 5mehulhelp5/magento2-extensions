<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Grouped Options for Magento 2
 */

namespace Amasty\GroupedOptions\Model\ResourceModel;

use Amasty\GroupedOptions\Api\Data\GroupAttrValueInterface;
use Amasty\GroupedOptions\Api\GroupRepositoryInterface;

class GroupAttrValue extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init(GroupRepositoryInterface::TABLE_VALUES, GroupAttrValueInterface::ID);
    }
}
