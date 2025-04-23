<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\ComponentDeclaration;

use Magento\Framework\DataObject;
use Amasty\MegaMenuLite\Api\Component\ComponentDeclarationInterface;

class Account implements ComponentDeclarationInterface
{
    /**
     * @var DataObject[]
     */
    private $items;

    public function __construct(
        array $items = []
    ) {
        usort($items, function ($prev, $next) {
            return (int)$prev->getSortOrder() <=> (int)$next->getSortOrder();
        });

        $this->items = $items;
    }

    public function getDeclaration(): array
    {
        return [
            'items' => $this->getItemsData()
        ];
    }

    public function getItemsData(): array
    {
        $result = [];

        foreach ($this->items as $item) {
            if ($item->isVisible()) {
                $result[] = $item->getItemData();
            }
        }

        return array_values($result);
    }
}
