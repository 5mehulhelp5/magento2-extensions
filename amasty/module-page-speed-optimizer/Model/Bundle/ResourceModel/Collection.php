<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Page Speed Optimizer Base for Magento 2
 */

namespace Amasty\PageSpeedOptimizer\Model\Bundle\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(
            \Amasty\PageSpeedOptimizer\Model\Bundle\Bundle::class,
            \Amasty\PageSpeedOptimizer\Model\Bundle\ResourceModel\Bundle::class
        );
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
