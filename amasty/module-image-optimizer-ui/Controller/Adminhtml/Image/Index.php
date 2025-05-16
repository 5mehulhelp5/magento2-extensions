<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer UI for Magento 2 (System)
 */

namespace Amasty\ImageOptimizerUi\Controller\Adminhtml\Image;

use Amasty\ImageOptimizerUi\Controller\Adminhtml\AbstractImageSettings;
use Magento\Framework\Controller\ResultFactory;

class Index extends AbstractImageSettings
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_ImageOptimizer::image_settings');
        $resultPage->getConfig()->getTitle()->prepend(__('Image Folder Optimization Settings'));

        return $resultPage;
    }
}
