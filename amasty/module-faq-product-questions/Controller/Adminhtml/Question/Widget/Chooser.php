<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Controller\Adminhtml\Question\Widget;

use Amasty\Faq\Block\Adminhtml\Question\Widget\Chooser as QuestionChooser;
use Amasty\Faq\Controller\Adminhtml\AbstractWidgetChooserController;

class Chooser extends AbstractWidgetChooserController
{
    public const ADMIN_RESOURCE = 'Amasty_Faq::question';

    /**
     * @return string
     */
    public function getChooserGridClass()
    {
        return QuestionChooser::class;
    }
}
