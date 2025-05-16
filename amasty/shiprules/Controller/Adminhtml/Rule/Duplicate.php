<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Controller\Adminhtml\Rule;

/**
 * Duplicate action.
 */
class Duplicate extends \Amasty\CommonRules\Controller\Adminhtml\Rule\AbstractDuplicate
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Amasty_Shiprules::rule';
}
