<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Controller\Adminhtml\Category\Widget;

use Amasty\Faq\Block\Adminhtml\Category\Widget\Chooser as FaqCategoryChooser;
use Amasty\Faq\Controller\Adminhtml\AbstractWidgetChooserController;

class Chooser extends AbstractWidgetChooserController
{
    public const ADMIN_RESOURCE = 'Amasty_Faq::category';

    /**
     * @return string
     */
    public function getChooserGridClass()
    {
        return FaqCategoryChooser::class;
    }
}
