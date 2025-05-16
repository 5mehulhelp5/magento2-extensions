<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Page Speed Optimizer Base for Magento 2
 */

namespace Amasty\PageSpeedOptimizer\Model\DiagnosticResult\ResourceModel;

use Amasty\PageSpeedOptimizer\Model\DiagnosticResult\DiagnosticResult as DiagnosticResultModel;
use Amasty\PageSpeedOptimizer\Model\DiagnosticResult\ResourceModel\DiagnosticResult as DiagnosticResultResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(
            DiagnosticResultModel::class,
            DiagnosticResultResourceModel::class
        );
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
