<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Controller\Adminhtml\Rule;

/**
 * Rule save action.
 */
class Save extends \Amasty\CommonRules\Controller\Adminhtml\Rule\AbstractSave
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Amasty_Shiprules::rule';

    /**
     * @var string
     */
    protected $dataPersistorKey = \Amasty\Shiprules\Model\ConstantsInterface::DATA_PERSISTOR_FORM;

    protected function prepareData(&$data)
    {
        if (isset($data['rule']['conditions'])) {
            $data['conditions'] = $data['rule']['conditions'];
        }
        if (isset($data['rule']['actions'])) {
            $data['actions'] = $data['rule']['actions'];
        }
        unset($data['rule']);
    }
}
