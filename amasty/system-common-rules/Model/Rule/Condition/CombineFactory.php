<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Common Rules for Magento 2 (System)
 */

namespace Amasty\CommonRules\Model\Rule\Condition;

/**
 * Factory for @see \Amasty\CommonRules\Model\Rule\Condition\Combine;
 */
class CombineFactory extends \Magento\SalesRule\Model\Rule\Condition\CombineFactory
{
    /**
     * CombineFactory constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = ConditionBuilder::AMASTY_COMMON_RULES_PATH_TO_CONDITIONS . 'Combine'
    ) {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }
}
