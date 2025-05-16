<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Common Rules for Magento 2 (System)
 */

namespace Amasty\CommonRules\Model\Validator;

/**
 * Interface ModifierInterface
 */
interface ValidatorInterface
{
    /**
     * @param \Magento\Rule\Model\AbstractModel $rule
     * @param \Magento\Quote\Model\Quote\Item[] $items
     *
     * @return boolean
     */
    public function validate($rule, $items);
}
