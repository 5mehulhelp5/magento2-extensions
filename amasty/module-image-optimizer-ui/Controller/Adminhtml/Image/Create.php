<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer UI for Magento 2 (System)
 */

namespace Amasty\ImageOptimizerUi\Controller\Adminhtml\Image;

use Amasty\ImageOptimizerUi\Controller\Adminhtml\AbstractImageSettings;

class Create extends AbstractImageSettings
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
