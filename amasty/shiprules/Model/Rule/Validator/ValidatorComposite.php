<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\Rule\Validator;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Validator\AbstractValidator;
use Magento\Framework\Validator\ValidatorInterface;

class ValidatorComposite extends AbstractValidator
{
    /**
     * @var ValidatorInterface[]
     */
    private $validators;

    public function __construct(
        array $validators = []
    ) {
        $this->validators = $validators;
    }

    /**
     * @param AbstractModel $object
     * @return bool
     */
    public function isValid($object): bool
    {
        $result = true;
        $this->_clearMessages();

        foreach ($this->validators as $validator) {
            if (!$validator->isValid($object)) {
                $result = false;
                $this->_addMessages($validator->getMessages());
            }
        }

        return $result;
    }
}
