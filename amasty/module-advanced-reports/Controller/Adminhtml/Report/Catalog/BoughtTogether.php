<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Reports Base for Magento 2
 */

namespace Amasty\Reports\Controller\Adminhtml\Report\Catalog;

use Amasty\Reports\Controller\Adminhtml\Report as ReportController;
use Magento\Backend\Model\View\Result\Page;

class BoughtTogether extends ReportController
{
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'Amasty_Reports::reports_catalog_bought_together'
        );
    }

    public function execute()
    {
        $resultPage = $this->prepareResponse();

        if ($resultPage instanceof Page) {
            $resultPage->addBreadcrumb(__('Catalog'), __('Catalog'));
        }

        return $resultPage;
    }
}
