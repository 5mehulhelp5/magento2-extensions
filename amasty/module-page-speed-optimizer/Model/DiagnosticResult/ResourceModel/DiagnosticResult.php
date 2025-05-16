<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Page Speed Optimizer Base for Magento 2
 */

namespace Amasty\PageSpeedOptimizer\Model\DiagnosticResult\ResourceModel;

use Amasty\PageSpeedOptimizer\Model\DiagnosticResult\DiagnosticResult as DiagnosticResultModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class DiagnosticResult extends AbstractDb
{
    public const TABLE_NAME = 'amasty_page_speed_optimizer_diagnostic';

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, DiagnosticResultModel::RESULT_ID);
    }
}
