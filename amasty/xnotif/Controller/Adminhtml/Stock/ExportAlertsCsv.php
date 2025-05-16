<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Controller\Adminhtml\Stock;

use Magento\Framework\App\Filesystem\DirectoryList;

class ExportAlertsCsv extends \Magento\SalesRule\Controller\Adminhtml\Promo\Quote
{
    public const ADMIN_RESOURCE = 'Amasty_Xnotif::stock';

    /**
     * Export alerts as CSV file
     *
     * @return \Magento\Framework\App\ResponseInterface|null
     */
    public function execute()
    {
        $fileName = 'alerts.csv';
        $content = $this->_view->getLayout()->createBlock(
            \Amasty\Xnotif\Block\Adminhtml\Report\Grid::class
        )->getCSVFile($fileName);

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
