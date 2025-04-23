<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\ResourceModel\Label\Save;

use ArrayIterator;
use IteratorAggregate as IteratorAggregate;
use Traversable;

class AdditionalSaveActionsPool implements IteratorAggregate
{
    private const SORT_ORDER = 'sortOrder';
    private const ACTION = 'action';

    /**
     * @var array[]
     *
     * @example [
     *      [
     *          'sortOrder' => 12,
     *          'actions' => $action
     *      ]
     * ]
     */
    private $saveActions;

    public function __construct(
        $saveActions = []
    ) {
        $this->saveActions = $this->sortActions($saveActions);
    }

    private function sortActions($actionConfig): array
    {
        usort($actionConfig, function (array $configA, array $configB) {
            $sortOrderA = $configA[self::SORT_ORDER] ?? 0;
            $sortOrderB = $configB[self::SORT_ORDER] ?? 0;

            return $sortOrderA <=> $sortOrderB;
        });

        return $actionConfig;
    }

    public function getIterator(): Traversable
    {
        $actions = [];

        foreach ($this->saveActions as $actionConfig) {
            $action = $actionConfig[self::ACTION] ?? null;

            if ($action instanceof AdditionalSaveActionInterface) {
                $actions[] = $action;
            }
        }

        return new ArrayIterator($actions);
    }
}
