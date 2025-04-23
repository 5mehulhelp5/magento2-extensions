<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Rule\Condition;

use Magento\Framework\ObjectManagerInterface;
use Magento\Rule\Model\Condition\AbstractCondition;

class Factory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    public function create(string $type, array $data = []): AbstractCondition
    {
        $condition = $this->objectManager->create($type, $data);

        if (false === $condition instanceof AbstractCondition) {
            throw new \InvalidArgumentException(
                __('Object must be an instance of %1', AbstractCondition::class)->render()
            );
        }

        return $condition;
    }
}
