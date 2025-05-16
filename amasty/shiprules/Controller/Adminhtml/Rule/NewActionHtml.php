<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Controller\Adminhtml\Rule;

/**
 * Class for getting html of selected Action.
 */
class NewActionHtml extends \Amasty\CommonRules\Controller\Adminhtml\Rule\AbstractCondition
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Amasty_Shiprules::rule';

    public function execute()
    {
        $this->newConditions('actions');

        return $this->getResponse();
    }
}
