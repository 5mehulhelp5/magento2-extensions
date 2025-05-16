<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Image Optimizer Speed Size for Magento 2
 */

namespace Amasty\ImageOptimizerSpeedSize\Model\Config\Backend\Validation;

use Laminas\Validator\ValidatorInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Validator\AbstractValidator;

class ValidatorComposite extends AbstractValidator
{
    /**
     * @var ValidatorInterface[]
     */
    private $validators;

    public function __construct(
        array $validators = []
    ) {
        $this->initializeValidators($validators);
    }

    /**
     * @param Value $value
     *
     * @return bool
     */
    public function isValid($value): bool
    {
        $this->_clearMessages();

        $isValid = true;
        foreach ($this->validators as $validator) {
            if (!$validator->isValid($value)) {
                $this->_addMessages($validator->getMessages());
                $isValid = false;
                break;
            }
        }

        return $isValid;
    }

    private function initializeValidators(array $validators): void
    {
        foreach ($validators as $validator) {
            if (!($validator instanceof ValidatorInterface)) {
                throw new \LogicException(
                    sprintf('Validator must implement %s', ValidatorInterface::class)
                );
            }
            $this->validators[] = $validator;
        }
    }
}
