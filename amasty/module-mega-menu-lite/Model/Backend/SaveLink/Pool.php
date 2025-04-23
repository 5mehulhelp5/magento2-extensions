<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Backend\SaveLink;

class Pool
{
    /**
     * @var array
     */
    private $modifiers;

    public function __construct(
        array $modifiers = []
    ) {
        $this->setModifiers($modifiers);
    }

    /**
     * Sort modifiers by sort_order and save sorted objects.
     *
     * @param array $modifiers
     * @return void
     */
    protected function setModifiers(array $modifiers): void
    {
        usort($modifiers, function (array $modifierLeft, array $modifierRight) {
            $left = $modifierLeft['sort_order'] ?? 0;
            $right = $modifierRight['sort_order'] ?? 0;

            return $left <=> $right;
        });

        $this->modifiers = array_column($modifiers, 'object');
    }

    public function execute(array $inputData): array
    {
        foreach ($this->modifiers as $modifier) {
            if ($modifier instanceof DataCollectorInterface) {
                $inputData = $modifier->execute($inputData);
            }
        }

        return $inputData;
    }
}
